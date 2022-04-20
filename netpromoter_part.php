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
    $referenceCount = 0; //counter for the reference companies
    //Parse the comment
    foreach ($data as $businessScore) {
        if ($businessScore["companyName"] != "CEO Juice Client average") { //if the company is not the CEO Juice Client average)
            if ($businessScore["referenceData"] == 'true' or $businessScore["referenceData"] == true) { //if the company is a reference data company
                $referenceCompanies[$referenceCount] .= array( //add the company to the reference data array
                    "companyName" => $businessScore["companyName"], //add the company name
                    "companyScore" => $businessScore["npsScore"], //add the company score
                    "companyRank" => $businessScore["npsRank"] //add the company rank
                );  //end of array
                //printf("<div class='item'>" . $businessScore["companyName"] . "</div>"); //print the company name
                $referenceCount++; //increment the reference company counter
            } elseif ($businessScore["referenceData"] == 'false' or $businessScore["referenceData"] == false) { //if the company is not a reference data company then assume it is this company
                $thisCompany[] = array( //add the company to the this company array, there should only be one of these
                    "companyName" => $businessScore["companyName"], //add the company name
                    "companyScore" => $businessScore["npsScore"], //add the company score
                    "companyRank" => $businessScore["npsRank"] //add the company rank
                );  //end of array
                printf("<div class='item'>" . $businessScore["companyName"] . "</div>"); //print the company name
            } //end of else
        }
    }
    //print_r($thisCompany);
    //print_r($referenceCompanies);
?>
<div class="netpromoter scoreGauge">
    <?php foreach ($thisCompany as $company) {
            $ourScore = $company["companyScore"];
        }
        print_r($thisCompany);
        ?>
    <svg class="typeRange" height="165" width="330" view-box="0 0 330 165">
        <g class="scale" stroke="red"></g>
        <path class="outline" d="" />
        <path class="fill" d="" />
        <polygon class="needle" points="220,10 300,210 220,250 140,210" />
    </svg>
    <div class="output"><?php echo $ourScore; ?></div>
</div>
<?php } ?>
