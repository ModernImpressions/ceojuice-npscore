<?php
$ceonpsURL = "https://api.ceojuice.com/api/Processes/Nps?CustomerNumber=";
$ceonpsawardsURL = "https://api.ceojuice.com/widgets/npsawards?customernumber=";
$ceoCustNum = ot_get_option('ceojuice_customerid');
$ceoAPIKey = ot_get_option('ceojuice_apiauth');
//$format = new NumberFormatter("en", NumberFormatter::SPELLOUT);
$buildScoreURL = $ceonpsURL . $ceoCustNum . '&AuthKey=' . $ceoAPIKey;
$buildAwardsURL = $ceonpsawardsURL . $ceoCustNum . '&count=4&containerclass=npsaward';
$readAwardsData = @file_get_contents($buildAwardsURL, true);
$readScoreJson = @file_get_contents($buildScoreURL, true);
if ($readScoreJson === false) {
    //There is an error opening the file
} else {
    //Results are received from the CEOJuice API
    //Decode JSON
    $data = json_decode($readScoreJson, true);
    $thisCompany = array(); //array to hold this companies data
    $referenceCompanies = array();  //array to hold the reference data company names
    $referenceCount = 0; //counter for the reference companies
    //Parse the comment
    foreach ($data as $businessScore) {
        if ($businessScore["companyName"] != "CEO Juice Client average") { //if the company is not the CEO Juice Client average)
            if ($businessScore["referenceData"] == 'true' or $businessScore["referenceData"] == true) { //if the company is a reference data company
                $referenceCompanies[$referenceCount] = array( //add the company to the reference data array
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
                //printf("<div class='item'>" . $businessScore["companyName"] . "</div>"); //print the company name
            } //end of else
        }
    }
    //print_r($thisCompany);
    //print_r($referenceCompanies);
    wp_enqueue_style('ceojuice-npscore-style', get_template_directory_uri() . '/other_part/ceojuice-npscore/assets/css/nps-styles.min.css');
?>
<div class="container">
    <div class="row">
        <div class="col-sm">
            <div class="row">
                <div class="netpromoter scoreGauge">
                    <?php foreach ($thisCompany as $company) {
                            $ourScore = $company["companyScore"];
                            $ourRank = $company["companyRank"];
                            $ourCompany = $company["companyName"];
                        }
                        ?>
                    <script type="text/javascript">
                    var initVal = "<?= $ourScore ?>";
                    </script>
                    <svg class="typeRange" height="165" width="400" view-box="0 0 400 165">
                        <g class="scale" stroke="black"></g>
                        <path class="outline" d="" />
                        <path class="fill" d="" />
                        <polygon class="needle" points="220,10 300,210 220,250 140,210" />
                    </svg>
                    <div class="output"></div>
                </div>
            </div>
            <div class="row">
                <div class="netpromoter npsawards">
                    <?php if ($readAwardsData != false) {
                            $processedAwardsData = str_replace('<link href="/ZCJ_BSCustomClasses.css" rel="stylesheet">', "", $readAwardsData);
                            $processedAwardsData = str_replace('/ZCJ_BSCustomClasses.css', "", $readAwardsData);
                            $processedAwardsData = str_replace('style="width:0px"', "", $processedAwardsData);
                            $processedAwardsData = str_replace('zcj-img-fluid', "npsaward img", $processedAwardsData);
                            $npsAwardsData = $processedAwardsData; ?>
                    <?php echo $npsAwardsData; ?>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-sm">
            <div class="netpromoter referenceScores">
                <ul class="referenceScore-list">
                    <li class="referenceScore-item">
                        <span class="referenceCompanyName ourcompany"><?php echo $company["companyName"] ?></span>
                        <progress class="referenceCompanyScore ourscore" min="-100" max="100"
                            value="<?php echo $company["companyScore"] ?>">
                            <span><span><strong><?php echo $company["companyScore"] ?></strong></span><span><?php echo $company["companyScore"] ?></span></span>
                        </progress>
                        <span class="referenceCompany score"><?php echo $company["companyScore"] ?></span>
                    </li>
                    <?php foreach ($referenceCompanies as $company) { ?>
                    <li class="referenceScore-item">
                        <span class="referenceCompanyName"><?php echo $company["companyName"] ?></span>
                        <progress class="referenceCompanyScore" min="-100" max="100"
                            value="<?php echo $company["companyScore"] ?>">
                            <span><span><strong><?php echo $company["companyScore"] ?></strong></span><span><?php echo $company["companyScore"] ?></span></span>
                        </progress>
                        <span class="referenceCompany score"><?php echo $company["companyScore"] ?></span>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php }
wp_enqueue_script('ceojuice-npscore-script', get_template_directory_uri() . '/other_part/ceojuice-npscore/assets/js/nps-js.min.js', array('jquery'), '1.0.0', true);
?>
