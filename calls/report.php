<?php
error_reporting(E_ALL);

include_once './config.php';

Switch (isset($_POST['act']) ? $_POST['act'] : '') {
    case 'view':
        $start_date = $mysqli->real_escape_string($_POST['start_date']);
        $finish_date = $mysqli->real_escape_string($_POST['finish_date']);

        $query = "SELECT
            *
        FROM `tbl_numbers_for_calls` 
        WHERE 
            `date`>='".$start_date."'
            AND 
            `date`<='".$finish_date."' 
        ORDER BY `id` ASC
        ";
        
        $result = $mysqli->query($query);
   
        if ($result->num_rows > 0) {
            ?>
            <div class="r_project_name">Bunchesdirect.com</div>
            <table class="r_results">
                <tr>
                    <th>Date</th>
                    <th>Number</th>
                    <th>Form type</th>
                    <th>Step</th>
                    <th>Status</th>
                </tr>
                <?php
                while ($obj = $result->fetch_object()) {
                    ?>
                    <tr>
                        <td><?php echo $obj->date; ?></td>
                        <td><?php echo $obj->number; ?></td>
                        <td><?php echo $obj->form; ?></td>
                        <td><?php echo $obj->step; ?></td>
                        <td><?php echo $obj->status; ?></td>
                    </tr>
                    <?php
                }
                ?> 
            </table>
            <?php
        }
        $result->close();
        
        $query = "SELECT
            * 
        FROM `tbl_numbers_for_calls` 
        WHERE 
            `date`>='".$start_date."' 
            AND 
            `date`<='".$finish_date."' 
        ORDER BY `id` ASC
        ";
        
        $result = $mysqli->query($query);
   
        if ($result->num_rows > 0) {
            ?>
            <div class="r_project_name">Bunchesdirect.ca</div>
            <table class="r_results">
                <tr>
                    <th>Date</th>
                    <th>Number</th>
                    <th>Form type</th>
                    <th>Step</th>
                    <th>Status</th>
                </tr>
                <?php
                while ($obj = $result->fetch_object()) {
                    ?>
                    <tr>
                        <td><?php echo $obj->date; ?></td>
                        <td><?php echo $obj->number; ?></td>
                        <td><?php echo $obj->form; ?></td>
                        <td><?php echo $obj->step; ?></td>
                        <td><?php echo $obj->status; ?></td>
                    </tr>
                    <?php
                }
                ?> 
            </table>
            <?php
        }
        
        $result->close();
    break;

    default:
        ?>
        <!doctype html>
        <html lang="en">
            <head>
                <meta charset="utf-8">
                <title>Report</title>
                <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
                <script src="//code.jquery.com/jquery-1.10.2.js"></script>
                <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
                <link rel="stylesheet" type="text/css" href="./css/style.css" media="all" />
                <script>
                    $(function () {
                        $("#datepicker_start").datepicker({dateFormat: 'yy-mm-dd'});
                        $("#datepicker_finish").datepicker({dateFormat: 'yy-mm-dd'});
                    });

                    function ViewResults()
                    {
                        $('.no_loader_div').show();
                        var start_date = $('#datepicker_start').val();
                        var finish_date = $('#datepicker_finish').val();

                        $.ajax({
                            data: {
                                act: 'view',
                                start_date: start_date,
                                finish_date: finish_date
                            },
                            type: "POST",
                            dataType: "html",
                            url: "./report.php",
                            success: function (data)
                            {
                                $('.no_loader_div').hide();
                                $('#results').html(data);
                            }
                        });
                    }
                </script>
            </head>
            <body>

                <p>Date start - finish: <input type="text" id="datepicker_start"> - <input type="text" id="datepicker_finish"> <input type="button" class="extension_button" onclick="ViewResults();" value="View results" /></p>
                <div class="no_loader_div" style="display: none;"><img src="./images/loader.gif" alt="loading..." /></div>
                <div id="results"></div>
            </body>
        </html>
        <?php
    break;
}

$mysqli->close();

?>