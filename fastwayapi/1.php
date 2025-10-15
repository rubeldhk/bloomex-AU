<script src="https://cdnjs.cloudflare.com/ajax/libs/processing.js/1.4.1/processing-api.min.js"></script><html>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js"></script>
    <!--
      Created using jsbin.com
      Source can be edited via http://jsbin.com/pdfjs-helloworld-v2/8598/edit
    -->
    <body>
        <div id="wrap" style="
             width: 660px;
             overflow: hidden;
             height: 429px;
             ">
            <div id="wrapper" style="margin-left:  -117px;">

            </div>
        </div>
        <!-- Use latest PDF.js build from Github -->
        <script type="text/javascript" src="https://rawgithub.com/mozilla/pdf.js/gh-pages/build/pdf.js"></script>

        <script type="text/javascript">
     
            //
            // Disable workers to avoid yet another cross-origin issue (workers need the URL of
            // the script to be loaded, and dynamically loading a cross-origin script does
            // not work)
            //
            var maxlabels = 3;
            var pages = new Array(0);
                    PDFJS.disableWorker = true;
            //
            // Asynchronous download PDF as an ArrayBuffer
            //
            PDFJS.getDocument('./123.pdf').then(function getPdfHelloWorld(pdf) {
                //
                // Fetch the first page
                //
                console.log(pdf);
                for (var curpage = 1; curpage <= Math.ceil(maxlabels / 2); curpage++) {
                    console.log(curpage + "===" + Math.ceil(maxlabels / 2));
                    pdf.getPage(curpage).then(function getPageHelloWorld(page) {
                        var scale = 1.5;
                        var viewport = page.getViewport(scale);
                        //
                        // Prepare canvas using PDF page dimensions
                        //
                        var canvas = document.createElement("canvas");
                        pages.push(canvas);
                        var wrapper = document.getElementById("wrapper");
                        wrapper.appendChild(canvas);
                        var context = canvas.getContext('2d');
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;
                        //
                        // Render PDF page into canvas context
                        //
                        var task = page.render({canvasContext: context, viewport: viewport})
                        task.promise.then(function () {
                           
                        });
                        curpage = curpage + 1;
                    });
                }
                console.log(pages);
                //    document.getElementById('wrap').style.display = "none";
            }, function (error) {
                console.log(error);
            });

        </script>
        <div id="print" style="display:none">
        </div>

        <input type="button" onclick="showprint()" value="Print"/>
    </body>
</html>

