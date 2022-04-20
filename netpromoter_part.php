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
        if ($businessScore["companyName"] != "CEO Juice Client average") { //if the company is not the CEO Juice Client average
            $companyName = $businessScore["companyName"]; //get the company name
            $companyScore = $businessScore["npsScore"]; //get the company score
            $companyRank = $businessScore["npsRank"]; //get the company rank
            $isReferenceData = $businessScore["ReferenceData"]; //get the reference data flag
            if ($isReferenceData == true) { //if the company is a reference data company
                $referenceCompanies[] = array( //add the company to the reference data array
                    "companyName" => $companyName, //add the company name
                    "companyScore" => $companyScore, //add the company score
                    "companyRank" => $companyRank //add the company rank
                );  //end of array
            } else $isReferenceData == false; { //if the company is not a reference data company then assume it is this company
                $thisCompany["companyName"] = $companyName; //add the company name
                $thisCompany["npsScore"] = $companyScore; //add the company score
                $thisCompany["npsRank"] = $companyRank; //add the company rank
            } //end of else
        }
    }
    print_r($thisCompany . "\n");
    print_r($referenceCompanies . "\n");
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
