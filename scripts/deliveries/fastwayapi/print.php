<?php
//require_once 'configuration.php';
//$cfg = new FastWayCfg();

$order_id = isset($_GET['order_id']) ? (int) $_GET['order_id'] : 0;

$pdf_link = 'https://media.bloomex.ca/bloomex.com.au/fastway_labels/' . $_GET['user_id'] . '/' . $order_id . '.pdf';
//$pdf_link = 'https://media.bloomex.ca/bloomex.com.au/fastway_labels/24875/1131060.pdf';
?>
<html>
    <head>
        <script type="text/javascript">
            function printDocument(documentId) 
            {
                var doc = document.getElementById(documentId);
                
                if (typeof doc.print === 'undefined') 
                {    
                    console.log('undefined');
                    setTimeout(function(){printDocument(documentId);}, 1000);
                } 
                else 
                {
                    console.log('print');
                    doc.print();
                }
            }
            
            //window.onload = printDocument('pdfDocument');
        </script>
    </head>
    <body>
        

        <?php /*<iframe id="iFramePdf" src="<?php echo $pdf_link; ?>" style="display:none;"></iframe>*/?>
        <embed
        type="application/pdf"
        src="<?php echo $pdf_link; ?>"
        id="pdfDocument"
        width="100%"
        height="100%" hidden/>
        <?php /*
        <script type="text/javascript" >
            function printTrigger(elementId) {
                var getMyFrame = document.getElementById(elementId);
                getMyFrame.focus();
                getMyFrame.contentWindow.print();
            }
            window.onload = printTrigger('iFramePdf');
        </script>
         * 
         */?>
    </body>
</html>