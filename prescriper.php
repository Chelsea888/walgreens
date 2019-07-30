<?php
/**
 * @param $db
 * @param $cusid
 */
require "header.php";

if (!isPrescriber($db, $auth->getUserId())) {
    header("Location: /$PROJECTNAME/");
}
?>
<div class="container marketing">

    <div class="row pt-5 pb-5">
        <div class="col-lg-2"></div>
        <div class="col-lg-8">
<?php

$gwid = isset($_REQUEST['gwid']) ? $_REQUEST['gwid'] : '';
$drugs = isset($_REQUEST['drugs']) ? $_REQUEST['drugs']: array();
$qtys = isset($_REQUEST['qtys']) ? $_REQUEST['qtys'] : array();
$refills = isset($_REQUEST['refills']) ? $_REQUEST['refills'] : array();
$action = isset($_REQUEST['action']) ? $_REQUEST['action']: '';

if ($action == 'add_prescription') {
    $prescriber_id = $auth->getUserId();
    $rx_num = addPrescription_cus($db, $prescriber_id, $gwid, $drugs, $qtys, $refills);
    header("Location: /$PROJECTNAME/myprescriptions.php?rx_num=$rx_num");
    #addPrescription_drug($db,$drug_name);
} else {
    ?>
        <form id = 'pres_form' name='pres_form' method = 'post' action = 'prescriper.php' >
                <input type='hidden' name='action' value='add_prescription' />

                <h2>Create New Prescription</h2>

                <div class="form-row">
                <div class = 'form-group'>
                    <label id='gwidlabel' for='gwid'>GWID:&nbsp; </label>
                    <input type='text' class ='form-control' id='gwid' name='gwid' placeholder = 'Input patient GWID' />
                </div>
                </div>


                <div class = 'select-group form-group'>
                    <div class="row">
                        <div class="col-lg-4">
                            <label for="category">Category:</label>
                        </div>
                        <div class="col-lg-8">
                            <select id='category' name = 'category'>
                                <option value ='' selected = 'selected'>Please select category</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <label for="drug">Drug:</label>
                        </div>
                        <div class="col-lg-8">
                            <select id='drug' >
                                <option value = '' selected = 'selected'>Select Drugs</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <label for="qty">Quantity:</label>
                        </div>
                        <div class="col-lg-8">
                            <input id='qty' type = 'number',min = '1' max = '100' oninput="this.value = Math.abs(this.value)">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <label for="refill">Refill Option:</label>
                        </div>
                        <div class="col-lg-8">
                            <input id='refill' type = 'number',min = '1' max = '4' oninput="this.value = Math.abs(this.value)">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <button type='button' id='add_drug' class='btn btn-dark' >Add Drug</button>
                        </div>
                        <div class="col-lg-8 pb-lg-0">
                            Click this button to add drugs to the prescription
                        </div>
                    </div>
                </div>

                <div class="row">

                </div>

                <div>
                    <button type = 'submit' class='btn btn-primary'>Add Prescription</button>
                    <input type = 'button'class='btn btn-warning' value = 'Reset' onclick = 'refresh();' >
                </div>
            </form>
        </div>
        <div class="col-lg-2"></div>
    </div>
</div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
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
                    var div = $('<div>').attr({
                        class : "row"
                    });
                    var div1 = $('<div>').attr({
                        class : "col"
                    });
                    var drugstr = $( "#drug option:selected" ).val();
                    div1.append($('<label>').text('Drug: ' + drugstr.substring(drugstr.indexOf('||')+2)));
                    div1.append($('<input>').attr({
                        type: 'input',
                        name: 'drugs[]',
                        value: drugstr.substring(0, drugstr.indexOf('||'))
                    }));

                    div.append(div1);

                    var div2 =  $('<div>').attr({
                        class : "col"
                    });
                    div2.append($('<label>').text('Quantity'));
                    div2.append($('<input>').attr({
                        type: 'input',
                        name: 'qtys[]',
                        value: $( "#qty" ).val()
                    }));

                    div.append(div2);

                    var div3 =  $('<div>').attr({
                        class : "col"
                    });
                    div3.append($('<label>').text('Refill'));
                    div3.append($('<input>').attr({
                        type: 'input',
                        name: 'refills[]',
                        value: $( "#refill" ).val()
                    }));

                    div.append(div3);

                    $('#add_drug').parent().parent().after(div);
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
                            .attr("value", key+ '||' + value).text(value));
                    });
                })
            });

            // autocomplete gwid
            $("input#gwid").autocomplete({
                select: function( event, ui ) {
                    var w = ui.item.value;
                    var name = w.substring(0, w.lastIndexOf(' | '));
                    $('#gwid').val(w.substring(w.lastIndexOf(' ')+1));
                    $('#gwidlabel').text(name + "'s" + " GWID:")
                    return false;
                },
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
        //refresh page
        function refresh(){
            location.reload();
        }
    </script>

<?php
    require "footer.php";
}

