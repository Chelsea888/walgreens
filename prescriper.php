<?php
/**
 * @param $db
 * @param $cusid
 */
require "header.php";

/*function showDrugCategory($db)
{
    $sql = "select distinct category from drug_cate";

    $stmt = $db->query($sql);
    while ($row = $stmt->fetch()) {
        $category = $row['category'];
        echo "<option>" . $category . "</option>";
    }
}*/

function RXNumerExists($db, $rxnum)
{
    $rxnum_exists = false;
    $sql = "select rx_num from Prescription where rx_num = '$rxnum'";
    $stmt = $db->query($sql);
    while ($row = $stmt->fetch()) {
        $rxnum_exists = true;
    }
    return $rxnum_exists;
}

function generateRXnum($db)
{
    $rx_num = "RX" . rand(1000000, 9999999) . "-" . rand(1000, 9999);
    while (RXNumerExists($db, $rx_num)) {
        $rx_num = "RX" . rand(1000000, 9999999) . "-" . rand(1000, 9999);
    }
    return $rx_num;
}

function getDocName($db)
{
    global $auth;
    $doc_id = $auth->getUserId();
    $sql = "select first_name, last_name from Prescriber where prescriber_id = " . $doc_id;
    $stmt = $db->query($sql);
    while ($row = $stmt->fetch()) {
        $doc_firstn = $row['first_name'];
        $doc_lastn = $row['last_name'];
    }
    return array($doc_firstn, $doc_lastn);
}

function getCus_Id($db,$gwid)
{
    $sql = "select cus_id from Customer where gwid= '" .$gwid . "'";
    $stmt = $db->query($sql);
    while ($row = $stmt->fetch()) {
        $cus_id = $row['cus_id'];
    }
    return $cus_id;
}

function addPrescription_cus($db, $prescriber_id, $gwid, $drugs, $refills)
{
    $rx_num = generateRXnum($db);
    $cus_id = getCus_Id($db, $gwid);
    $rx_written_date = date("Y-m-d H:i:s");
    $req = $db->prepare("INSERT INTO Prescription ( rx_num, prescriber_id, cus_id, rx_written_date
                                               ) VALUES (
                                                    :rx_num,
                                                    :prescriber_id,
                                                    :cus_id,
                                                    :rx_written_date)");

    $req->execute(array(
        'rx_num' => $rx_num,
        'prescriber_id' => $prescriber_id,
        'cus_id' => $cus_id,
        'rx_written_date' => $rx_written_date));

    for($i=0; $i < count($drugs); $i++) {
        $sql = "INSERT INTO Prescription_drug (rx_num, NDC, refill) VALUES (:rx_num, :NDC, :refill)";
        $stmt = $db->prepare($sql);
        $stmt->execute(array('rx_num'=>$rx_num, 'NDC'=>$drugs[$i], 'refill'=>$refills[$i]));
    }
    return $rx_num;
}

$gwid = isset($_REQUEST['gwid']) ? $_REQUEST['gwid'] : '';
$drugs = isset($_REQUEST['drugs']) ? $_REQUEST['drugs']: array();
$refills = isset($_REQUEST['refills']) ? $_REQUEST['refills'] : array();
$action = isset($_REQUEST['action']) ? $_REQUEST['action']: '';

if ($action == 'add_prescription') {
    $prescriber_id = $auth->getUserId();
    $rx_num = addPrescription_cus($db, $prescriber_id, $gwid, $drugs, $refills);
    header("Location: /CJ_Project/myprescriptions.php?rx_num=$rx_num");
    #addPrescription_drug($db,$drug_name);
} else {
    echo "<form id = 'pres_form' name='pres_form' method = 'post' action = 'prescriper.php' >";
        echo "<fieldset>";
            echo "<legend >Create Prescription:</legend>";

            echo "<div class = 'input-group form-group'>";
                //echo "<input type = 'text' class = 'form-control' id = 'gwid' name = 'gwid' placeholder = 'Input patient GWID'/></div>";
            echo "<div class='ui-widget'>
                <label for='gwid'>GWID:&nbsp; </label>
                <input type='text' class ='form-control' id='gwid' name='gwid' placeholder = 'Input patient GWID' />
            </div>";

            echo "<div class = 'select-group form group'>";
                echo "<label for ='category'>Category:&nbsp;</label><select id='category' name = 'category'>";
                    echo "<option value ='' selected = 'selected'>Please select category</option>";
                    echo "</select>";

                echo "<lable>&nbsp;&nbsp;&nbsp; Drug:&nbsp;</lable><select id='drug' >";
                    echo "<option value = '' selected = 'selected'>Select Drugs</option>";
                    echo "</select>";

                echo "<label>&nbsp;Refill Option:&nbsp;";
                    echo "<input id='refill' type = 'number',min = '1' max = '4' oninput=\"this.value = Math.abs(this.value)\"></div>";

            echo "<div><button type='button' id='add_drug' type = '' class='btn btn-dark' >Add Drug</button></div>";

            echo "<div >";

                echo "<input type='hidden' name='action' value='add_prescription' />";
                echo "<button type = 'submit' class='btn btn-dark'>Add Prescription</button></div>";

            echo "</fieldset></form>";
    ?>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js" integrity="sha256-eGE6blurk5sHj+rmkfsGYeKyZx3M4bG+ZlFyA7Kns7E=" crossorigin="anonymous"></script>

    <script language="javascript">
        $(document).ready(function () {
            // popuate categories
            $('#category option:gt(0)').remove();
            $.getJSON("getdata.php", {'datatype': 'category'}, function(data) {
                var categories = data;

                $.each(categories, function(key, value) {
                    $('#category').append($("<option></option>")
                        .attr("value", value).text(value));
                });
            })

            // click add drug to add drug with refill option
            $('#add_drug').click(function() {
                if ($( "#drug option:selected" ) && $( "#drug option:selected" ).val()) {
                    var div = $('<div>');
                    div.append($('<label>').text('Drug'));
                    div.append($('<input>').attr({
                        type: 'input',
                        name: 'drugs[]',
                        value: $( "#drug option:selected" ).val()
                    }));
                    div.append($('<label>').text('Refill'));
                    div.append($('<input>').attr({
                        type: 'input',
                        name: 'refills[]',
                        value: $( "#refill" ).val()
                    }));
                    $('#add_drug').parent().after(div);
                }
            });

            // select category to filter drugs
            $('#category').on('change', function() {
                var optionSelected = $("option:selected", this);
                var categorySelected = this.value;

                $('#drug option:gt(0)').remove();
                $.getJSON("getdata.php", {'datatype' : 'drug', 'category' : categorySelected}, function(data) {
                    var drugs = data;

                    $.each(drugs, function(key,value) {
                        $('#drug').append($("<option></option>")
                            .attr("value", key).text(value));
                    });
                })
            });

            // autocomplete gwid
            $("input#gwid").autocomplete({
                source: function( request, response ) {
                    $.ajax( {
                        url: "getdata.php",
                        dataType: "json",
                        data: {
                            datatype: "gwid",
                            term: request.term
                        },
                        success: function( data ) {
                            response(data);
                        }
                    } );
                },
                minLength: 0
            }).bind('focus', function(){ $(this).autocomplete("search"); } );
        });
    </script>
    </body>
    </html>

<?php
}

