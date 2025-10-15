<?php
//
defined('_VALID_MOS') or die('Restricted access');

require_once( $mainframe->getPath('admin_html') );


$id_postcode = intval(mosGetParam($_REQUEST, 'id', 0));
?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>

<script>
$(function() {
  $( "#datepicker" ).datepicker();
});
</script>

<script type="text/javascript">
    function ActiveCode(id)
    {
        var active = $("#active"+id).val();

        $.ajax({
                type: "POST",
                url: "index2.php?option=com_postcode&task=active",
                data: {id: id, active: active},
                dataType: 'html',
                cache: false,
                success: function(responce)
                {
                    document.location.reload();
                }
        });

    }
</script>

<?php
Switch (isset($task) ? $task : '')
{
    case 'edit':
        edit($id_postcode);
    break;

    case 'delete':
        delete($id_postcode);
    break;

    case 'add':
        add();
    break;

    case 'active':
        active();
    break;

    case 'save':
        save($id_postcode);
    break;

    default:
        show();
}


function show()
{
    global $database;

    $query = "SELECT * FROM `jos_vm_postcode` ORDER BY `id` DESC";

    $database->setQuery($query);
    $result = $database->loadObjectList();

    HTML_postcode::show($result);
}

function edit($id)
{
    global $database;

    $query = "SELECT * FROM `jos_vm_postcode` WHERE `id`='$id' LIMIT 1";

    $database->setQuery($query);
    $result = $database->loadObjectList();
    $result = $result[0];

    HTML_postcode::edit($result);
}

function save($id)
{
     global $database;

     if (isset($_POST['update']))
     {
         $query = "UPDATE `jos_vm_postcode` SET `postcodes`='".$_POST['postcodes']."', `date_others`='".$_POST['date_others']."', `reason`='".mysql_real_escape_string($_POST['reason'])."' WHERE `id`='$id' LIMIT 1";

         $database->setQuery($query);
         $database->query();
     }

     echo 'Save was successful.';
     show();
}

function delete($id)
{
   global $database;

   $query = "DELETE FROM `jos_vm_postcode` WHERE `id`='$id'";

   $database->setQuery($query);
   $database->query();

   echo 'Removal was successful.';

   show();
}

function add()
{
    global $database;

    if (isset($_POST['insert']))
    {

       $query = "INSERT INTO `jos_vm_postcode` (`postcodes`, `date_others`, `reason`) VALUES ('".$_POST['postcodes']."', '".$_POST['date_others']."', '".$database->getEscaped($_POST['reason'])."')";

       $database->setQuery($query);
       $database->query();
    }

    echo 'Add was successful.';
    show();
}

function active()
{
    global $database;

    $id = (int)$_POST['id'];
    $active = (int)$_POST['active'];

    $query = "UPDATE `jos_vm_postcode` SET `active`='$active' WHERE `id`='$id' LIMIT 1";

    $database->setQuery($query);
    $database->query();
}

?>