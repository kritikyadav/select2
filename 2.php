<?php
    if (isset($_GET["p1"]) && isset($_GET["p2"])) {
        $singleSelect = $_GET["p1"] ;
        $multipleSelect = array();
        $multipleSelect = explode(",", $_GET["p2"]);
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        
    </style>

    <title>select2</title>
</head>

<body>
<?php
  $dsn_common_db = array(
    'username'   =>  "devuser",
    'password'   =>  "d3v7u5s4e2R1",
    'hostspec'   =>  "QCINDSRV",
    'database'   =>  "QCDEV",
  );

  $serverName = $dsn_common_db['hostspec'];
  $uid = $dsn_common_db['username'];
  $pwd = $dsn_common_db['password'];
  $db = $dsn_common_db['database'];

  $connectionInfo = array("UID" => $uid, "PWD" => $pwd, "Database" => $db, "CharacterSet" => "UTF-8");
  $conn = sqlsrv_connect($serverName, $connectionInfo);
  if ($conn == false) {
    echo 'unable to connect...';
  }
  $sql = "SELECT * FROM kyoptions";
  $folderData = sqlsrv_query($conn, $sql);
  if ($folderData === false) {
    die(print_r(sqlsrv_errors(), true));
  }
  $folders_arr = array();
  while ($row = sqlsrv_fetch_array($folderData, SQLSRV_FETCH_ASSOC)) {
    $folders_arr[] = array(
        "value" => $row['value'],
        "option" => $row['option'],
    );
  }
?>
    <div id="myModal" class="modal fade">
        <label>Single__Select</label>
        <select id='singleselect' class="singleselect" >
        <option  value=''></option>
            <?php 
            foreach($folders_arr as $options)
                { ?>
            <option  value='<?php echo $options['option']?>' <?php if($singleSelect != '' && $singleSelect == $options['option']){ echo "selected='selected'";}?>>
                <?php echo $options['option']?>
            </option>
            <?php } ?>
        </select>
        <br><br>
        <label>MultipleSelect</label>
        <select id='multipleselect' class="multipleselect" name="states[]" multiple="multiple">
        <?php 
            foreach($folders_arr as $options)
                { ?>
            <option  value='<?php echo $options['option']?>' <?php if($multipleSelect != ''){ foreach($multipleSelect as $opt){ if($opt == $options['option']) { echo "selected='selected'";}}} ?>>
                <?php echo $options['option']?>
            </option>
            <?php } ?>
        </select>
    <input type="submit" id="enable" value="Enable"><input type="submit" id="disable" value="Disable">
    <br>
    <input type='submit' id='save' value='Save' onclick="savingdata()">
    </div>
    <script>
        $(document).ready(function () {
           
            $('.singleselect').select2({
                width: '200px',
                placeholder: "Select a state",
                allowClear: true,
                theme: "classic" // set theme
            });
            //multiple select 2 
            $('.multipleselect').select2({
                placeholder: 'Select required option',
                width: '200px',
                allowClear: false,
                tags: true,
                tokenSeparators: [',', ' '],
                theme: "classic" // set theme
            });
            var singleselected = $('.singleselect').val();
            var multipleselected = $('.multipleselect').val();
            /* if you want to disable the select dropdown then 2 buttons can be used such that 1 enables it and other disables using class of the button for ex*/
            /*onclick this will enable the select dropdown by making disabled=false */
            $("#enable").on("click", function () {
                $(".singleselect").prop("disabled", false);
                $(".multipleselect").prop("disabled", false);
            });
            /*onclick this will disable the select dropdown by making disabled=True */
            $("#disable").on("click", function () {
                $(".singleselect").prop("disabled", true);
                $(".multipleselect").prop("disabled", true);
            });

        });
       function savingdata(){
            var singleSelect = $(".singleselect").val();
            var multipleSelect = $(".multipleselect").val();
            location.href = "2.php?p1=" + singleSelect + "&p2=" + multipleSelect;
            var selectedValues = new Array();
            // selectedValues = <?php //if ($multipleSelect != ''){echo json_encode($multipleSelect);} ?>";
            //     $.each($(".multipleselect"), function(){
            //         $(this).select2('val', selectedValues);
            //     }); 

        }
    </script>
<!--
selectOnClose is used to select last value which was highlighted before closing the dropdown.
$('#mySelect2').select2({
  selectOnClose: true
});

closeOnSelect  is used to either close or keep dropdown open once the value is selected. 
$('#mySelect2').select2({
  closeOnSelect: false
});
 
limiting the number of selections. 
$(".js-example-basic-multiple-limit").select2({
  maximumSelectionLength: 2
});

when set to true a 'x' icon will show if any value is selected then it will help to clear the selected options.
$('select').select2({
  placeholder: 'This is my placeholder',
  allowClear: true
});

only start searching when the user has input 3 or more characters
$('select').select2({
  minimumInputLength: 3 
});

only allow terms up to 20 characters long
$('select').select2({
    maximumInputLength: 20 
});

at least 20 results must be displayed
$('select').select2({
    minimumResultsForSearch: 20 
});

hide search field for the single select2 
$("#js-example-basic-hide-search").select2({
    minimumResultsForSearch: Infinity
});

no hide control available for multiple select so to disable search for multi select we need to set the disabled property. 
$('#js-example-basic-hide-search-multi').select2();
$('#js-example-basic-hide-search-multi').on('select2:opening select2:closing', function( event ) {
    var $searchfield = $(this).parent().find('.select2-search__field');
    $searchfield.prop('disabled', true);
});

Set the value, creating a new option if not exists
if ($('#mySelect2').find("option[value='" + data.id + "']").length) {
    $('#mySelect2').val(data.id).trigger('change');
} else { 
    // Create a DOM Option and pre-select by default
    var newOption = new Option(data.text, data.id, true, true); // 3rd parameter true means selected, 4th parameter will actualy set selected.
    // Append it to the select
    $('#mySelect2').append(newOption).trigger('change');
} 


$('#mySelect2').val('1'); // Select the option with a value of '1'
$('#mySelect2').trigger('change'); // Notify any JS components that the value changed


$('#mySelect2').val(null).trigger('change'); // Clearning selection


$('#mySelect2').find(':selected'); // find the selected options

$('#mySelect2').select2('open'); // open the dropdown using code
$('#mySelect2').select2('close');// close the dropdown using code

language translation 
$(".js-example-language").select2({
  language: "es"
});





 -->
</body>

</html>


