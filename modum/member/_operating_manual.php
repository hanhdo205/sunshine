<script src="js/custom/pdf.js"></script>
<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	<li class="breadcrumb-item active"><?php echo T_('Operating manual');?></li>
	</ol>
	<div class="container-fluid">
		<div class="animated fadeIn">
			<div class="row">
				<div class="col-md-12 mb-5">
					<div class="card">
						<div class="card-header"><?php echo T_('Operating manual');?>
							<div class="card-header-actions">
								<button id="zoom_plus"><?php echo T_('+');?></button>
								<button id="zoom_minus"><?php echo T_('-');?></button>
								<button id="prev"><?php echo T_('Previous');?></button>
								<button id="next"><?php echo T_('Next');?></button>
								  &nbsp; &nbsp;
								<span><?php echo T_('Page:');?> <span id="page_num"></span> / <span id="page_count"></span></span>
							</div>
						</div>
						<div class="card-body text-center">
							<canvas id="the-canvas"></canvas>
							<?php
							switch($rowgetInfo["roles_id"]) {
								case 15:
									$path='manual/customer/';
									break;
								default:
									$path='manual/admin/';
									break;
							}
							switch($_SESSION["language"]) {
								case 'vi_VN':
									$folder='vi_VN-manual.pdf/';
									break;
								case 'ja_JP':
									$folder='ja_JP-manual.pdf/';
									break;
								default:
									$folder='en_US-manual.pdf/';
									break;
							}
							$file='104.pdf';
							?>
						</div>
					</div> <!-- card -->
				</div> <!-- col-md-12 -->
				<!-- /.col-->
			</div>
			<!-- /.row-->
		</div>
	</div>
</main>
<script type="text/javascript">
// If absolute URL from the remote server is provided, configure the CORS
// header on that server. https://mozilla.github.io/pdf.js/web/viewer.html
var url = '<?php echo $path.$folder.$file;?>';

// Loaded via <script> tag, create shortcut to access PDF.js exports.
var pdfjsLib = window['pdfjs-dist/build/pdf'];

// The workerSrc property shall be specified.
pdfjsLib.GlobalWorkerOptions.workerSrc = 'js/custom/pdf.worker.js';

var scale = 1;


var pdfDoc = null,
    pageNum = 1,
    pageRendering = false,
    pageNumPending = null,
    //scale = 1.5,
    canvas = document.getElementById('the-canvas'),
    ctx = canvas.getContext('2d');

$("#zoom_plus").on('click', function() {
	scale = scale + 0.2;
	var page = parseInt($('#page_num').text());
	renderPage(page);
});

$("#zoom_minus").on('click', function() {
	scale = scale - 0.2;
	var page = parseInt($('#page_num').text());
	renderPage(page);
});

/**
 * Get page info from document, resize canvas accordingly, and render page.
 * @param num Page number.
 */
function renderPage(num) {
  pageRendering = true;
  // Using promise to fetch the page
  pdfDoc.getPage(num).then(function(page) {
    var viewport = page.getViewport({scale: scale});
    canvas.height = viewport.height;
    canvas.width = viewport.width;

    // Render PDF page into canvas context
    var renderContext = {
      canvasContext: ctx,
      viewport: viewport
    };
    var renderTask = page.render(renderContext);

    // Wait for rendering to finish
    renderTask.promise.then(function() {
      pageRendering = false;
      if (pageNumPending !== null) {
        // New page rendering is pending
        renderPage(pageNumPending);
        pageNumPending = null;
      }
    });
  });

  // Update page counters
  document.getElementById('page_num').textContent = num;
}

/**
 * If another page rendering in progress, waits until the rendering is
 * finised. Otherwise, executes rendering immediately.
 */
function queueRenderPage(num) {
  if (pageRendering) {
    pageNumPending = num;
  } else {
    renderPage(num);
  }
}

/**
 * Displays previous page.
 */
function onPrevPage() {
  if (pageNum <= 1) {
    return;
  }
  pageNum--;
  queueRenderPage(pageNum);
}
document.getElementById('prev').addEventListener('click', onPrevPage);

/**
 * Displays next page.
 */
function onNextPage() {
  if (pageNum >= pdfDoc.numPages) {
    return;
  }
  pageNum++;
  queueRenderPage(pageNum);
}
document.getElementById('next').addEventListener('click', onNextPage);

/**
 * Asynchronously downloads PDF.
 */
pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
  pdfDoc = pdfDoc_;
  document.getElementById('page_count').textContent = pdfDoc.numPages;

  // Initial/first page rendering
  renderPage(pageNum);
});
</script>
