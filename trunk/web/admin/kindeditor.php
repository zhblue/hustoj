        <meta charset="utf-8" />
        <link rel="stylesheet" href="../kindeditor/themes/default/default.css" />
        <link rel="stylesheet" href="../kindeditor/plugins/code/prettify.css" />
        <script charset="utf-8" src="../kindeditor/kindeditor.js"></script>
        <script charset="utf-8" src="../kindeditor/lang/zh_CN.js"></script>
        <script charset="utf-8" src="../kindeditor/plugins/code/prettify.js"></script>
        <script>
var kindeditorSeted=false;
if(!kindeditorSeted){

        $(document).ready(window.setTimeout(function (){
                KindEditor.ready(function(K) {
                        let editor1 = K.create('textarea[class="kindeditor"]', {
                                width : '100%',
                                cssPath : '../kindeditor/plugins/code/prettify.css',
                                uploadJson : '../kindeditor/php/upload_json.php',
                                fileManagerJson : '../kindeditor/php/file_manager_json.php',
                                allowFileManager : false,
                                allowImageRemote: true,
                                filterMode:false,
                                cssData: 'body { font-family:"Consolas";font-size: 18px;line-height:150% }  ',
<?php if(isset($OJ_MARKDOWN)&&$OJ_MARKDOWN)
                                echo "designMode:false,";
?>

                                afterCreate : function() {
                                        var self = this;
                                        K.ctrl(document, 13, function() {
                                                self.sync();
                                        });
                                        K.ctrl(self.edit.doc, 13, function() {
                                                self.sync();
                                        });
                                }
                                ,
                                        afterBlur: function() {
                                                var self = this;
                                                self.sync();
                                        }

                                ,
                                afterChange: function() {
                                        var self = this;
                                        self.sync();
                                        if( typeof sync === "function")         sync();
                                }
                        });
                        prettyPrint();
                });
        }),100);
         kindeditorSeted=true;
}

        </script>

