<?php
	class PlaceLookup
	{
		protected $oDB;

		protected $iPlaceID;

		protected $sType = false;

		protected $aLangPrefOrder = array();

		protected $bAddressDetails = false;

		protected $bExtraTags = false;

		protected $bNameDetails = false;

		function PlaceLookup(&$oDB)
		{
			$this->oDB =& $oDB;
		}

		function setLanguagePreference($aLangPrefOrder)
		{
			$this->aLangPrefOrder = $aLangPrefOrder;
		}

		function setIncludeAddressDetails($bAddressDetails = true)
		{
			$this->bAddressDetails = $bAddressDetails;
		}

		function setIncludeExtraTags($bExtraTags = false)
		{
			if ((float) CONST_Postgresql_Version > 9.2)
			{
				$this->bExtraTags = $bExtraTags;
			}
		}

		function setIncludeNameDetails($bNameDetails = false)
		{
			if ((float) CONST_Postgresql_Version > 9.2)
			{
				$this->bNameDetails = $bNameDetails;
			}
		}

		function setPlaceID($iPlaceID)
		{
			$this->iPlaceID = $iPlaceID;
		}

		function setOSMID($sType, $iID)
		{
			$sSQL = "select place_id from placex where osm_type = '".pg_escape_string($sType)."' and osm_id = ".(int)$iID." order by type = 'postcode' asc";
			$this->iPlaceID = $this->oDB->getOne($sSQL);
		}

		function lookupPlace($details)
		{
			if (isset($details['place_id'])) $this->iPlaceID = $details['place_id'];
			if (isset($details['type'])) $this->sType = $details['type'];
			if (isset($details['osm_type']) && isset($details['osm_id']))
			{
				$this->setOSMID($details['osm_type'], $details['osm_id']);
			}

			return $this->lookup();
		}

		function lookup()
		{
			if (!$this->iPlaceID) return null;

			$sLanguagePrefArraySQL = "ARRAY[".join(',',array_map("getDBQuoted", $this->aLangPrefOrder))."]";

			if ($this->sType == 'tiger')
			{
				$sSQL = "select place_id,partition, 'T' as osm_type, place_id as osm_id, 'place' as class, 'house' as type, null as admin_level, housenumber, null as street, null as isin, postcode,";
				$sSQL .= " 'us' as country_code, parent_place_id, null as linked_place_id, 30 as rank_address, 30 as rank_search,";
				$sSQL .= " coalesce(null,0.75-(30::float/40)) as importance, null as indexed_status, null as indexed_date, null as wikipedia, 'us' as calculated_country_code, ";
				$sSQL .= " get_address_by_language(place_id, $sLanguagePrefArraySQL) as langaddress,";
				$sSQL .= " null as placename,";
				$sSQL .= " null as ref,";
				if ($this->bExtraTags) $sSQL .= " null as extra,";
				if ($this->bNameDetails) $sSQL .= " null as names,";
				$sSQL .= " st_y(centroid) as lat,";
				$sSQL .= " st_x(centroid) as lon";
				$sSQL .= " from location_property_tiger where place_id = ".(int)$this->iPlaceID;
			}
			else
			{
				$sSQL = "select placex.place_id, partition, osm_type, osm_id, class, type, admin_level, housenumber, street, isin, postcode, country_code, parent_place_id, linked_place_id, rank_address, rank_search, ";
				$sSQL .= " coalesce(importance,0.75-(rank_search::float/40)) as importance, indexed_status, indexed_date, wikipedia, calculated_country_code, ";
				$sSQL .= " get_address_by_language(place_id, $sLanguagePrefArraySQL) as langaddress,";
				$sSQL .= " get_name_by_language(name, $sLanguagePrefArraySQL) as placename,";
				$sSQL .= " get_name_by_language(name, ARRAY['ref']) as ref,";
				if ($this->bExtraTags) $sSQL .= " hstore_to_json(extratags) as extra,";
				if ($this->bNameDetails) $sSQL .= " hstore_to_json(name) as names,";
				$sSQL .= " (case when centroid is null then st_y(st_centroid(geometry)) else st_y(centroid) end) as lat,";
				$sSQL .= " (case when centroid is null then st_x(st_centroid(geometry)) else st_x(centroid) end) as lon";
				$sSQL .= " from placex where place_id = ".(int)$this->iPlaceID;
			}

			$aPlace = $this->oDB->getRow($sSQL);


			if (PEAR::IsError($aPlace))
			{
				failInternalError("Could not lookup place.", $sSQL, $aPlace);
			}

			if (!$aPlace['place_id']) return null;

			if ($this->bAddressDetails)
			{
				$aAddress = $this->getAddressNames();
				$aPlace['aAddress'] = $aAddress;
			}

			if ($this->bExtraTags)
			{
				if ($aPlace['extra'])
				{
					$aPlace['sExtraTags'] = json_decode($aPlace['extra']);
				}
				else
				{
					$aPlace['sExtraTags'] = (object) array();
				}
			}

			if ($this->bNameDetails)
			{
				if ($aPlace['names'])
				{
					$aPlace['sNameDetails'] = json_decode($aPlace['names']);
				}
				else
				{
					$aPlace['sNameDetails'] = (object) array();
				}
			}

			$aClassType = getClassTypes();
			$sAddressType = '';
			$sClassType = $aPlace['class'].':'.$aPlace['type'].':'.$aPlace['admin_level'];
			if (isset($aClassType[$sClassType]) && isset($aClassType[$sClassType]['simplelabel']))
			{
				$sAddressType = $aClassType[$aClassType]['simplelabel'];
			}
			else
			{
				$sClassType = $aPlace['class'].':'.$aPlace['type'];
				if (isset($aClassType[$sClassType]) && isset($aClassType[$sClassType]['simplelabel']))
					$sAddressType = $aClassType[$sClassType]['simplelabel'];
				else $sAddressType = $aPlace['class'];
			}

			$aPlace['addresstype'] = $sAddressType;

			return $aPlace;
		}

		function getAddressDetails($bAll = false)
		{
			if (!$this->iPlaceID) return null;

			$sLanguagePrefArraySQL = "ARRAY[".join(',',array_map("getDBQuoted", $this->aLangPrefOrder))."]";

			$sSQL = "select *,get_name_by_language(name,$sLanguagePrefArraySQL) as localname from get_addressdata(".$this->iPlaceID.")";
			if (!$bAll) $sSQL .= " WHERE isaddress OR type = 'country_code'";
			$sSQL .= " order by rank_address desc,isaddress desc";

			$aAddressLines = $this->oDB->getAll($sSQL);
			if (PEAR::IsError($aAddressLines))
			{
				var_dump($aAddressLines);
				exit;
			}
			return $aAddressLines;
		}

		function getAddressNames()
		{
			$aAddressLines = $this->getAddressDetails(false);

			$aAddress = array();
			$aFallback = array();
			$aClassType = getClassTypes();
			foreach($aAddressLines as $aLine)
			{
				$bFallback = false;
				$aTypeLabel = false;
				if (isset($aClassType[$aLine['class'].':'.$aLine['type'].':'.$aLine['admin_level']])) $aTypeLabel = $aClassType[$aLine['class'].':'.$aLine['type'].':'.$aLine['admin_level']];
				elseif (isset($aClassType[$aLine['class'].':'.$aLine['type']])) $aTypeLabel = $aClassType[$aLine['class'].':'.$aLine['type']];
				elseif (isset($aClassType['boundary:administrative:'.((int)($aLine['rank_address']/2))]))
				{
					$aTypeLabel = $aClassType['boundary:administrative:'.((int)($aLine['rank_address']/2))];
					$bFallback = true;
				}
				else
				{
					$aTypeLabel = array('simplelabel'=>'address'.$aLine['rank_address']);
					$bFallback = true;
				}
				if ($aTypeLabel && ((isset($aLine['localname']) && $aLine['localname']) || (isset($aLine['housenumber']) && $aLine['housenumber'])))
				{
					$sTypeLabel = strtolower(isset($aTypeLabel['simplelabel'])?$aTypeLabel['simplelabel']:$aTypeLabel['label']);
					$sTypeLabel = str_replace(' ','_',$sTypeLabel);
					if (!isset($aAddress[$sTypeLabel]) || (isset($aFallback[$sTypeLabel]) && $aFallback[$sTypeLabel]) || $aLine['class'] == 'place')
					{
						$aAddress[$sTypeLabel] = $aLine['localname']?$aLine['localname']:$aLine['housenumber'];
					}
					$aFallback[$sTypeLabel] = $bFallback;
				}
			}
			return $aAddress;
		}

	}
?>
