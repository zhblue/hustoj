<?php $show_title=isset($id) ? "$id - Source Compare - $OJ_NAME" : "Source Compare - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<link type="text/css" rel="stylesheet" href="mergely/codemirror.css" />
<link type="text/css" rel="stylesheet" href="mergely/mergely.css" />

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-arrow-left-right"></i> Source Compare</h4>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <input type="checkbox" id="ignorews" class="form-check-input">
            <label class="form-check-label" for="ignorews">Ignore whitespace</label>
        </div>
        <div class="mb-2">
            <tt id="path-lhs"></tt> &nbsp; <a id="save-lhs" class="save-link text-decoration-none" href="#">save</a>
            &nbsp;|&nbsp;
            <tt id="path-rhs"></tt> &nbsp; <a id="save-rhs" class="save-link text-decoration-none" href="#">save</a>
        </div>
        <div id="mergely-resizer" style="height: 500px;">
            <div id="compare"></div>
        </div>
    </div>
</div>

<script type="text/javascript" src="mergely/codemirror.js"></script>
<script type="text/javascript" src="mergely/mergely.js"></script>
<script>
$(document).ready(function(){
    $('#compare').mergely({
        width: 'auto',
        height: 'auto',
        cmsettings: { readOnly: false },
    });

    var lhs_url = 'getsource.php?id=<?php echo isset($_GET['left']) ? intval($_GET['left']) : 0?>';
    var rhs_url = 'getsource.php?id=<?php echo isset($_GET['right']) ? intval($_GET['right']) : 0?>';

    $.ajax({
        type: 'GET', async: true, dataType: 'text',
        url: lhs_url,
        success: function(response){
            $('#path-lhs').text(lhs_url);
            $('#compare').mergely('lhs', response);
        }
    });
    $.ajax({
        type: 'GET', async: true, dataType: 'text',
        url: rhs_url,
        success: function(response){
            $('#path-rhs').text(rhs_url);
            $('#compare').mergely('rhs', response);
        }
    });

    function downloadContent(a, side){
        var txt = $('#compare').mergely('get', side);
        var datauri = "data:plain/text;charset=UTF-8," + encodeURIComponent(txt);
        a.setAttribute('download', side + ".txt");
        a.setAttribute('href', datauri);
    }

    document.getElementById('save-lhs').addEventListener('mouseover', function(){ downloadContent(this, "lhs"); }, false);
    document.getElementById('save-rhs').addEventListener('mouseover', function(){ downloadContent(this, "rhs"); }, false);
    document.getElementById('ignorews').addEventListener('change', function(){
        $('#compare').mergely('options', { ignorews: this.checked });
    }, false);
});
</script>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
