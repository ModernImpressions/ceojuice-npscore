<?php
$ceoURL = "https://api.ceojuice.com/api/Processes/Nps?CustomerNumber=";
$ceoCustNum = ot_get_option('ceojuice_customerid');
$ceoAPIKey = ot_get_option('ceojuice_apiauth');
//$format = new NumberFormatter("en", NumberFormatter::SPELLOUT);
$buildURL = $ceoURL . $ceoCustNum . '&AuthKey=' . $ceoAPIKey;

$readjson = @file_get_contents($buildURL, true);
if ($readjson === false) {
    //There is an error opening the file
} else {
    //Results are received from the CEOJuice API
    //Decode JSON
    $data = json_decode($readjson, true);
    $thisCompany = array(); //array to hold this companies data
    $referenceCompanies = array();  //array to hold the reference data company names
    //Parse the comment
    foreach ($data as $businessScore) {
        if ($businessScore["companyName"] != "CEO Juice Client average") {
            $companyName = $businessScore["companyName"];
            $companyScore = $businessScore["npsScore"];
            $companyRank = $businessScore["npsRank"];
            $isReferenceData = $businessScore["ReferenceData"];
            if ($isReferenceData == true) {
                $referenceCompanies["companyName"] = $companyName;
                $referenceCompanies["npsScore"] = $companyScore;
                $referenceCompanies["npsRank"] = $companyRank;
            } else {
                $thisCompany["companyName"] = $companyName;
                $thisCompany["npsScore"] = $companyScore;
                $thisCompany["npsRank"] = $companyRank;
            }
        }
    }
    print_r($thisCompany);
    print_r($referenceCompanies);
?>
<div class="netpromoter scoreGauge">
    <?php $ourScore = $thisCompany["npsScore"] ?>
    <svg class="typeRange" height="165" width="330" view-box="0 0 330 165">
        <g class="scale" stroke="red"></g>
        <path class="outline" d="" />
        <path class="fill" d="" />
        <polygon class="needle" points="220,10 300,210 220,250 140,210" />
    </svg>
    <div class="output"><?php echo $ourScore; ?></div>
</div>
<?php } ?>
