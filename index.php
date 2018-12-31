<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/processing.js/1.4.1/processing-api.min.js"></script><html>
<script src='tesseract.min.js'></script>
<script src="pdf.js"></script>
<body>
	<button type="button" id="to-ocr">To OCR</button>
	<div id="op-canvas" style="display: block;"></div>
	<input id='pdf' type='file'/>
	<div id="page_num"> </div>
	<div id="ocr_results"> ocr result </div>
	<div id="ocr_status"> </div>	
	<script type="text/javascript">

		//
		// Disable workers to avoid yet another cross-origin issue (workers need the URL of
		// the script to be loaded, and dynamically loading a cross-origin script does
		// not work)
		//
		PDFJS.disableWorker = true;

		//
		// Asynchronous download PDF as an ArrayBuffer
		//
		var pdf = document.getElementById('pdf');
		pdf.onchange = function(ev) {
			if (file = document.getElementById('pdf').files[0]) {
				fileReader = new FileReader();
				fileReader.onload = function(ev) {
					PDFJS.getDocument(fileReader.result).then(function getPdfHelloWorld(pdf) {
						totalPages = pdf.numPages;
						for (var i = 1; i <= totalPages; i++) {							
							pdf.getPage(i).then(function getPageHelloWorld(page) {
							var scale = 1.5;
							var viewport = page.getViewport(scale);


							//
							// Prepare canvas using PDF page dimensions
							//
							var canvas = document.createElement('canvas');
							var context = canvas.getContext('2d');
							canvas.height = viewport.height;
							canvas.width = viewport.width;
							canvas.className = 'c_class';
							// canvas.id = 'canvas_'+i;
							document.getElementById('op-canvas').appendChild(canvas);

							//
							// Render PDF page into canvas context
							//
							var task = page.render({canvasContext: context, viewport: viewport})
							task.promise.then(function(){
							});
						});
					}
					}, function(error){
						console.log(error);
					});
				};
				fileReader.readAsArrayBuffer(file);
			}
		}
	</script>
	<script type="text/javascript">
		$(document).on('click','#to-ocr',function(){
			$('.c_class').each(function(i, obj){
				Tesseract.recognize(obj.toDataURL()).then(function(result) {
					$.ajax({
						method : 'POST',
						url: "save.php",
						data : {'ocr_text' : result.text},
						success: function(result){
						}
					});
				}).progress(function(result) {
					document.getElementById("ocr_status").innerText = result["status"] + " (" +(result["progress"] * 100) + "%)";
				});
			})
		});
	</script>
</body>
</html>