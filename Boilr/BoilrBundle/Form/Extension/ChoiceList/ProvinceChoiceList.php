<?php

namespace Boilr\BoilrBundle\Form\Extension\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;

class ProvinceChoiceList implements ChoiceListInterface
{
    /**
     * Stores the available province choices
     * @var array
     */
    protected static $provinces;

    protected $format;
	
    public function __construct($format = "%k - %v") 
    {
        $this->format = $format;
    }
    
    /**
     * Returns the italian province choices.
     *
     * @return array The province choices
     */
    public function getChoices()
    {
        if (null == static::$provinces) {
            static::$provinces = array(
                       "AG" => "Agrigento",            "AL" => "Alessandria",
                       "AN" => "Ancona",               "AO" => "Aosta",
                       "AQ" => "Aquila",               "AR" => "Arezzo",
                       "AP" => "Ascoli-Piceno",        "AT" => "Asti",
                       "AV" => "Avellino",             "BA" => "Bari",
                       "BT" => "Barletta-Andria-Trani","BL" => "Belluno",
                       "BN" => "Benevento",            "BG" => "Bergamo",
                       "BI" => "Biella",               "BO" => "Bologna",
                       "BZ" => "Bolzano",              "BS" => "Brescia",
                       "BR" => "Brindisi",             "CA" => "Cagliari",
                       "CL" => "Caltanissetta",        "CB" => "Campobasso",
                       "CE" => "Caserta",              "CT" => "Catania",
                       "CZ" => "Catanzaro",            "CH" => "Chieti",
                       "CO" => "Como",                 "CS" => "Cosenza",
                       "CR" => "Cremona",              "KR" => "Crotone",
                       "CN" => "Cuneo",                "EN" => "Enna",
                       "FM" => "Fermo",                "FE" => "Ferrara",
                       "FI" => "Firenze",              "FG" => "Foggia",
                       "FC" => "Forli-Cesena",         "FR" => "Frosinone",
                       "GE" => "Genova",               "GO" => "Gorizia",
                       "GR" => "Grosseto",             "IM" => "Imperia",
                       "IS" => "Isernia",              "SP" => "La-Spezia",
                       "LT" => "Latina",               "LE" => "Lecce",
                       "LC" => "Lecco",                "LI" => "Livorno",
                       "LO" => "Lodi",                 "LU" => "Lucca",
                       "MC" => "Macerata",             "MN" => "Mantova",
                       "MS" => "Massa-Carrara",        "MT" => "Matera",
                       "ME" => "Messina",              "MI" => "Milano",
                       "MO" => "Modena",               "MB" => "Monza-Brianza",
                       "NA" => "Napoli",               "NO" => "Novara",
                       "NU" => "Nuoro",                "OR" => "Oristano",
                       "PD" => "Padova",               "PA" => "Palermo",
                       "PR" => "Parma",                "PV" => "Pavia",
                       "PG" => "Perugia",              "PU" => "Pesaro-Urbino",
                       "PE" => "Pescara",              "PC" => "Piacenza",
                       "PI" => "Pisa",                 "PT" => "Pistoia",
                       "PN" => "Pordenone",            "PZ" => "Potenza",
                       "PO" => "Prato",                "RG" => "Ragusa",
                       "RA" => "Ravenna",              "RC" => "Reggio-Calabria",
                       "RE" => "Reggio-Emilia",        "RI" => "Rieti",
                       "RN" => "Rimini",               "Roma" => "Roma",
                       "RO" => "Rovigo",               "SA" => "Salerno",
                       "SS" => "Sassari",              "SV" => "Savona",
                       "SI" => "Siena",                "SR" => "Siracusa",
                       "SO" => "Sondrio",              "TA" => "Taranto",
                       "TE" => "Teramo",               "TR" => "Terni",
                       "TO" => "Torino",               "TP" => "Trapani",
                       "TN" => "Trento",               "TV" => "Treviso",
                       "TS" => "Trieste",              "UD" => "Udine",
                       "VA" => "Varese",               "VE" => "Venezia",
                       "VB" => "Verbania",             "VC" => "Vercelli",
                       "VR" => "Verona",               "VV" => "Vibo-Valentia",
                       "VI" => "Vicenza",              "VT" => "Viterbo");
        }
		
        foreach (static::$provinces as $key => $value) {
                $label = str_replace(array("%k", "%v"), array($key, $value), $this->format);
                $result[$key] = $label;
        }

        return $result;
    }
}
