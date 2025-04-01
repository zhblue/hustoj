// JavaScript Document
(function(K) {
    var ZYFILE = {
        fileInput: null, // 选择文件按钮dom对象
        uploadInput: null, // 上传文件按钮dom对象
        dragDrop: null, //拖拽敏感区域
        url: "", // 上传action路径
        filePostName: "imgFile", // 上传action路径
        uploadFile: [], // 需要上传的文件数组
        lastUploadFile: [], // 上一次选择的文件数组，方便继续上传使用
        perUploadFile: [], // 存放永久的文件数组，方便删除使用
        fileNum: 0, // 代表文件总个数，因为涉及到继续添加，所以下一次添加需要在它的基础上添加索引
        /* 提供给外部的接口 */
        filterFile: function(files) { // 提供给外部的过滤文件格式等的接口，外部需要把过滤后的文件返回
            return files;
        },
        onSelect: function(selectFile, files) { // 提供给外部获取选中的文件，供外部实现预览等功能  selectFile:当前选中的文件  allFiles:还没上传的全部文件
        },
        onDelete: function(file, files) { // 提供给外部获取删除的单个文件，供外部实现删除效果  file:当前删除的文件  files:删除之后的文件
        },
        onProgress: function(file, loaded, total) { // 提供给外部获取单个文件的上传进度，供外部实现上传进度效果
        },
        onSuccess: function(file, responseInfo) { // 提供给外部获取单个文件上传成功，供外部实现成功效果
        },
        onFailure: function(file, responseInfo) { // 提供给外部获取单个文件上传失败，供外部实现失败效果
        },
        onComplete: function(responseInfo) { // 提供给外部获取全部文件上传完成，供外部实现完成效果
        },
        /* 内部实现功能方法 */
        // 获得选中的文件
        //文件拖放
        funDragHover: function(e) {
            e.stopPropagation();
            e.preventDefault();
            this[e.type === "dragover" ? "onDragOver" : "onDragLeave"].call(e.target);
            return this;
        },
        // 获取文件
        funGetFiles: function(e) {
            var self = this;
            // 取消鼠标经过样式
            this.funDragHover(e);
            // 从事件中获取选中的所有文件
            var files = e.target.files || e.dataTransfer.files;
            self.lastUploadFile = this.uploadFile;
            this.uploadFile = this.uploadFile.concat(this.filterFile(files));
            var tmpFiles = [];
            // 因为jquery的inArray方法无法对object数组进行判断是否存在于，所以只能提取名称进行判断
            var lArr = []; // 之前文件的名称数组
            var uArr = []; // 现在文件的名称数组
            $.each(self.lastUploadFile, function(k, v) {
                lArr.push(v.name);
            });
            $.each(self.uploadFile, function(k, v) {
                uArr.push(v.name);
            });
            $.each(uArr, function(k, v) {
                // 获得当前选择的每一个文件   判断当前这一个文件是否存在于之前的文件当中
                if ($.inArray(v, lArr) < 0) { // 不存在
                    tmpFiles.push(self.uploadFile[k]);
                }
            });
            // 如果tmpFiles进行过过滤上一次选择的文件的操作，需要把过滤后的文件赋值
            //if(tmpFiles.length!=0){
            this.uploadFile = tmpFiles;
            //}
            // 调用对文件处理的方法
            this.funDealtFiles();
            return true;
        },
        // 处理过滤后的文件，给每个文件设置下标
        funDealtFiles: function() {
            var self = this;
            // 目前是遍历所有的文件，给每个文件增加唯一索引值
            $.each(this.uploadFile, function(k, v) {
                // 因为涉及到继续添加，所以下一次添加需要在总个数的基础上添加
                v.index = self.fileNum;
                // 添加一个之后自增
                self.fileNum++;
            });
            // 先把当前选中的文件保存备份
            var selectFile = this.uploadFile;
            // 要把全部的文件都保存下来，因为删除所使用的下标是全局的变量
            this.perUploadFile = this.perUploadFile.concat(this.uploadFile);
            // 合并下上传的文件
            this.uploadFile = this.lastUploadFile.concat(this.uploadFile);
            // 执行选择回调
            this.onSelect(selectFile, this.uploadFile);
            return this;
        },
        // 处理需要删除的文件  isCb代表是否回调onDelete方法  
        // 因为上传完成并不希望在页面上删除div，但是单独点击删除的时候需要删除div   所以用isCb做判断
        funDeleteFile: function(delFileIndex, isCb) {
            var self = this; // 在each中this指向没个v  所以先将this保留
            var tmpFile = []; // 用来替换的文件数组
            // 合并下上传的文件
            var delFile = this.perUploadFile[delFileIndex];
            // 目前是遍历所有的文件，对比每个文件  删除
            $.each(this.uploadFile, function(k, v) {
                if (delFile != v) {
                    // 如果不是删除的那个文件 就放到临时数组中
                    tmpFile.push(v);
                }
            });
            this.uploadFile = tmpFile;
            if (isCb) { // 执行回调
                // 回调删除方法，供外部进行删除效果的实现
                self.onDelete(delFile, this.uploadFile);
            }
            return true;
        },
        // 上传多个文件
        funUploadFiles: function() {
            var self = this; // 在each中this指向没个v  所以先将this保留
            // 遍历所有文件  ，在调用单个文件上传的方法
            $.each(this.uploadFile, function(k, v) {
                self.funUploadFile(v);
            });
        },
        // 上传单个个文件
        funUploadFile: function(file) {
            var self = this; // 在each中this指向没个v  所以先将this保留
            var formdata = new FormData();
            formdata.append(self.filePostName, file);
            var xhr = new XMLHttpRequest();
            // 绑定上传事件
            // 进度
            xhr.upload.addEventListener("progress", function(e) {
                // 回调到外部
                self.onProgress(file, e.loaded, e.total);
            }, false);
            // 完成
            xhr.addEventListener("load", function(e) {
                // 从文件中删除上传成功的文件  false是不执行onDelete回调方法
                self.funDeleteFile(file.index, false);
                // 回调到外部
                self.onSuccess(file, xhr.responseText);
                if (self.uploadFile.length == 0) {
                    // 回调全部完成方法
                    self.onComplete("全部完成");
                }
            }, false);
            // 错误
            xhr.addEventListener("error", function(e) {
                // 回调到外部
                self.onFailure(file, xhr.responseText);
            }, false);
            xhr.open("POST", self.url, true);
            //xhr.setRequestHeader("X_FILENAME", file.name);
            xhr.send(formdata);
        },
        // 返回需要上传的文件
        funReturnNeedFiles: function() {
            return this.uploadFile;
        },
        // 初始化
        init: function() { // 初始化方法，在此给选择、上传按钮绑定事件
            var self = this; // 克隆一个自身
            self.uploadFile = []; // 需要上传的文件数组
            self.lastUploadFile = []; // 上一次选择的文件数组，方便继续上传使用
            self.perUploadFile = []; // 存放永久的文件数组，方便删除使用
            self.fileNum = 0; // 代表文件总个数，因为涉及到继续添加，所以下一次添加需要在它的基础上添加索引
            if (this.dragDrop) {
                this.dragDrop.addEventListener("dragover", function(e) {
                    self.funDragHover(e);
                }, false);
                this.dragDrop.addEventListener("dragleave", function(e) {
                    self.funDragHover(e);
                }, false);
                this.dragDrop.addEventListener("drop", function(e) {
                    self.funGetFiles(e);
                }, false);
            }
            // 如果选择按钮存在
            if (self.fileInput) {
                // 绑定change事件
                this.fileInput.addEventListener("change", function(e) {
                    self.funGetFiles(e);
                    this.value = '';
                }, false);
            }
            // 如果上传按钮存在
            if (self.uploadInput) {
                // 绑定click事件
                this.uploadInput.addEventListener("click", function(e) {
                    self.funUploadFiles(e);
                }, false);
            }
        }
    };
    $.fn.zyUpload = function(options, param) {
        var otherArgs = Array.prototype.slice.call(arguments, 1);
        if (typeof options == 'string') {
            var fn = this[0][options];
            if ($.isFunction(fn)) {
                return fn.apply(this, otherArgs);
            } else {
                throw ("zyUpload - No such method: " + options);
            }
        }
        return this.each(function() {
            var para = {}; // 保留参数
            var self = this; // 保存组件对象
            var defaults = {
                itemWidth: "140px", // 文件项的宽度
                itemHeight: "120px", // 文件项的高度
                url: "/upload/UploadAction", // 上传文件的路径
                filePostName: "imgFile", // name值
                multiple: true, // 是否可以多个文件上传
                dragDrop: true, // 是否可以拖动上传文件
                del: true, // 是否可以删除文件
                finishDel: false, // 是否在上传文件完成后删除预览
                /* 提供给外部的接口方法 */
                onSelect: function(selectFiles, files) {}, // 选择文件的回调方法  selectFile:当前选中的文件  allFiles:还没上传的全部文件
                onDelete: function(file, files) {}, // 删除一个文件的回调方法 file:当前删除的文件  files:删除之后的文件
                onSuccess: function(file) {}, // 文件上传成功的回调方法
                onFailure: function(file) {}, // 文件上传失败的回调方法
                onComplete: function(responseInfo) {}, // 上传完成的回调方法
            };
            para = $.extend(defaults, options);
            this.init = function() {
                this.createHtml(); // 创建组件html
                this.createCorePlug(); // 调用核心js
            };
            /**
             * 功能：创建上传所使用的html
             * 参数: 无
             * 返回: 无
             */
            this.createHtml = function() {
                var multiple = ""; // 设置多选的参数
                para.multiple ? multiple = "multiple" : multiple = "";
                var html = '';
                html += [
                    '<div class="ke-swfupload">',
                    '<div class="ke-swfupload-top">',
                    '<div class="ke-inline-block ke-swfupload-button">',
                    '<span class="ke-button-common ke-button-outer">',
                    '<input type="button" class="ke-button-common ke-button webuploader_pick" value="选择文件" />',
                    '</span>',
                    '</div>',
                    '<div class="ke-inline-block ke-swfupload-desc">' + options.uploadDesc + '<span class="status_info"></span></div>',
                    //'<span class="ke-button-common ke-button-outer ke-swfupload-startupload">',
                    //'<input type="button" class="ke-button-common ke-button upload_btn" value="' + options.startButtonValue + '" />',
                    //'</span>',
                    '</div>',
                    '<div class="ke-swfupload-body upload_preview preview" style="height:350px;margin-bottom:10px;"></div>', //图片上传区域
                    //add20210923
                    '<div class="file_dig" style="display:none; position: absolute;z-index:99999; width:654px; height:510px; top: 50%; left:50%;margin-top: -255px;margin-left: -327px;background:rgba(0,0,0,0.1);text-align:center; line-height:494px;color:red; font-size:18px;" >正常上传中！！！</div>',

                    '</div>',
                    '<form class="uploadForm" action="' + para.url + '" method="post" enctype="multipart/form-data">',
                    '<input class="fileImage" style="display:none;" type="file" size="30" name="fileImage" ' + multiple + '>',
                    '<button type="button" onclick="confirm_upload()" class="ke-button-common upload_submit_btn fileSubmit" style="border:1px solid #E4E4E4;border-left:none;border-top:none;border-bottom:2px solid #DDD;border-radius:2px;font-size:12px;color:red;" >确认上传文件</button>', //add ke-button-common
                    '</form>',
                ].join('');

                $(self).append(html);
                $(self).data('uploadList', []);
                // 初始化html之后绑定按钮的点击事件
                this.addEvent();
            };
            /**
             * 功能：显示统计信息和绑定继续上传和上传按钮的点击事件
             * 参数: 无
             * 返回: 无
             */
            this.funSetStatusInfo = function(files) {
                return;
            };
            /**
             * 功能：过滤上传的文件格式等
             * 参数: files 本次选择的文件
             * 返回: 通过的文件
             */
            this.funFilterEligibleFile = function(files) {
                var arrFiles = []; // 替换的文件数组
                for (var i = 0, file; file = files[i]; i++) {
                    if (file.size >= 51200000) {
                        alert('您这个"' + file.name + '"文件大小过大');
                    } else {
                        // 在这里需要判断当前所有文件中
                        arrFiles.push(file);
                    }
                }
                return arrFiles;
            };
            /**
             * 功能： 处理参数和格式上的预览html
             * 参数: files 本次选择的文件
             * 返回: 预览的html
             */
            this.funDisposePreviewHtml = function(file, e) {
                var html = "";
                var imgWidth = parseInt(para.itemWidth.replace("px", "")) - 5;

                // 处理配置参数删除按钮
                var delHtml = "";
                if (para.del) { // 显示删除按钮
                    delHtml = '<span class="file_del" data-index="' + file.index + '" title="删除" style="cursor:pointer;">删除</span>';
                }

                // 处理不同类型文件代表的图标
                var fileImgSrc = "control/images/fileType/";
                if (file.type.indexOf("rar") > 0) {
                    fileImgSrc = fileImgSrc + "rar.png";
                } else if (file.type.indexOf("zip") > 0) {
                    fileImgSrc = fileImgSrc + "zip.png";
                } else if (file.type.indexOf("text") > 0) {
                    fileImgSrc = fileImgSrc + "txt.png";
                } else {
                    fileImgSrc = fileImgSrc + "file.png";
                }
                // 图片上传的是图片还是其他类型文件 //弹出框图片展示区域
                if (file.type.indexOf("image") == 0) {
                    html += '<div class="upload_append_list uploadList_' + file.index + '" style=" float:left;width:100px;margin:0px 2px; ">';
                    html += ' <div class="file_bar">';
                    html += ' <div style="padding:1px;">';
                    html += '  <p class="file_name" style="text-align:center;width:100px; height:41px;line-height:20px; padding-bottom:3px; marigin:0px; background:#EEE; overflow:hidden; ">' + file.name + '</p>';
                    html += delHtml; // 删除按钮的html
                    html += '		</div>';
                    html += '	</div>';
                    html += '	<a style="cursor:default; float:left; margin-top:-38px; width:' + para.itemWidth + ';height:' + para.itemHeight + ';line-height:' + para.itemHeight + ';" href="#" >';
                    html += '	<img class="upload_image uploadImage_' + file.index + '" src="' + e.target.result + '" style="width:100px; height:80px;" />';
                    /* html += '		<div class="uploadImg" style="width:'+para.itemWidth+'px;)">';                                                                 
                    html += '		</div>'; */
                    html += '	</a>';
                    html += '	<p class="file_progress uploadProgress_' + file.index + '" ></p>';
                    //html += '	<p class="file_failure uploadFailure_' + file.index + '" >上传失败，请重试</p>'; //I DO
                    html += '	<p class="file_success uploadSuccess_' + file.index + '" ></p>';
                    html += '</div>';


                } else {
                    html += '<div class="upload_append_list uploadList_' + file.index + '">';
                    html += '	<div class="file_bar">';
                    html += '		<div style="padding:5px;">';
                    html += '			<p class="file_name">' + file.name + '</p>';
                    html += delHtml; // 删除按钮的html
                    html += '		</div>';
                    html += '	</div>';

                    html += '	<a style="width:' + para.itemWidth + ';height:' + para.itemHeight + ';line-height:' + para.itemHeight + ';" href="#" >';
                    html += '			<img class="upload_image uploadImage_' + file.index + '" src="' + fileImgSrc + '"/>';
                    html += '	</a>';
                    html += '	<p class="file_progress uploadProgress_' + file.index + '" ></p>';
                    //html += '	<p class="file_failure uploadFailure_' + file.index + '" >上传失败，请重试</p>'; //I 
                    DO
                    html += '	<p class="file_success uploadSuccess_' + file.index + '" ></p>';
                    html += '</div>';
                }

                return html;
            };


            /**
             * 功能：调用核心插件
             * 参数: 无
             * 返回: 无
             */
            this.createCorePlug = function() {
                var params = {
                    fileInput: $(self).find(".fileImage").get(0),
                    uploadInput: $(self).find(".fileSubmit").get(0),
                    dragDrop: $(self).find(".fileDragArea").get(0),
                    url: $(self).find(".uploadForm").attr("action"),
                    filePostName: para.filePostName,

                    filterFile: function(files) {
                        // 过滤合格的文件
                        return self.funFilterEligibleFile(files);
                    },
                    onSelect: function(selectFiles, allFiles) {
                        para.onSelect(selectFiles, allFiles); // 回调方法
                        self.funSetStatusInfo(ZYFILE.funReturnNeedFiles()); // 显示统计信息
                        var html = '',
                            i = 0;
                        // 组织预览html
                        var funDealtPreviewHtml = function() {
                            file = selectFiles[i];
                            if (file) {
                                var reader = new FileReader()
                                reader.onload = function(e) {
                                    // 处理下配置参数和格式的html
                                    html += self.funDisposePreviewHtml(file, e);
                                    i++;
                                    // 再接着调用此方法递归组成可以预览的html
                                    funDealtPreviewHtml();
                                }
                                reader.readAsDataURL(file);
                            } else {
                                // 走到这里说明文件html已经组织完毕，要把html添加到预览区
                                funAppendPreviewHtml(html);
                            }
                        };

                        // 添加预览html
                        var funAppendPreviewHtml = function(html) {
                            // 添加到添加按钮前
                            $(self).find(".preview").append(html);
                            // 绑定删除按钮
                            funBindDelEvent();
                            funBindHoverEvent();
                        };

                        // 绑定删除按钮事件
                        var funBindDelEvent = function() {
                            if ($(self).find(".file_del").length > 0) {
                                // 删除方法
                                $(self).find(".file_del").click(function() {
                                    ZYFILE.funDeleteFile(parseInt($(this).attr("data-index")), true);
                                    return false;
                                });
                            }

                            if ($(self).find(".file_edit").length > 0) {
                                // 编辑方法
                                $(self).find(".file_edit").click(function() {
                                    // 调用编辑操作
                                    //ZYFILE.funEditFile(parseInt($(this).attr("data-index")), true);
                                    return false;
                                });
                            }
                        };

                        // 绑定显示操作栏事件
                        var funBindHoverEvent = function() {
                            $(self).find(".upload_append_list").hover(
                                function(e) {
                                    $(this).find(".file_bar").addClass("file_hover");
                                },
                                function(e) {
                                    $(this).find(".file_bar").removeClass("file_hover");
                                }
                            );
                        };

                        funDealtPreviewHtml();
                    },
                    onDelete: function(file, files) {
                        // 移除效果
                        $(self).find(".uploadList_" + file.index).fadeOut();
                        // 重新设置统计栏信息
                        self.funSetStatusInfo(files);
                        var list = $(self).data('uploadList');
                        $.each(list, function(k, v) {
                            if (file.index == v.index) {
                                list.splice(k, 1);
                            }
                        })
                    },
                    onProgress: function(file, loaded, total) {
                        var eleProgress = $(self).find(".uploadProgress_" + file.index),
                            percent = (loaded / total * 100).toFixed(2) + '%';
                        if (eleProgress.is(":hidden")) {
                            eleProgress.show();
                        }
                        eleProgress.css("width", percent);
                    },
                    onSuccess: function(file, response) {
                        file.res = JSON.parse(response);
                        $(self).data('uploadList').push(file);
                        $(self).find(".uploadProgress_" + file.index).hide();
                        $(self).find(".uploadSuccess_" + file.index).show();
                        //$(self).find(".uploadInf").append("<p>上传成功，文件地址是：" + response + "</p>");
                        // 根据配置参数确定隐不隐藏上传成功的文件
                        if (para.finishDel) {
                            // 移除效果
                            $(self).find(".uploadList_" + file.index).fadeOut();
                            // 重新设置统计栏信息
                            self.funSetStatusInfo(ZYFILE.funReturnNeedFiles());
                        }
                    },
                    onFailure: function(file) {
                        $(self).find(".uploadProgress_" + file.index).hide();
                        $(self).find(".uploadSuccess_" + file.index).show();
                        $(self).find(".uploadInf").append("<p>文件" + file.name + "上传失败！</p>");
                        //$(self).find(".uploadImage_" + file.index).css("opacity", 0.2);
                    },
                    onComplete: function(response) {

                    },
                    onDragOver: function() {
                        $(this).addClass("upload_drag_hover");
                    },
                    onDragLeave: function() {
                        $(this).removeClass("upload_drag_hover");
                    }
                };

                ZYFILE = $.extend(ZYFILE, params);
                ZYFILE.init();
            };
            /**
             * 功能：绑定事件
             * 参数: 无
             * 返回: 无
             */
            this.addEvent = function() {
                // 如果快捷添加文件按钮存在
                if ($(self).find(".filePicker").length > 0) {
                    // 绑定选择事件
                    $(self).find(".filePicker").bind("click", function(e) {
                        $(self).find(".fileImage").click();
                    });
                }

                // 绑定继续添加点击事件
                $(self).find(".webuploader_pick").bind("click", function(e) {
                    $(self).find(".fileImage").click();
                });

                // 绑定上传点击事件
                $(self).find(".upload_btn").bind("click", function(e) {
                    // 判断当前是否有文件需要上传
                    if (ZYFILE.funReturnNeedFiles().length > 0) {
                        $(self).find(".fileSubmit").click();
                    } else {
                        alert("请先选中文件再点击上传");
                    }
                });

                // 如果快捷添加文件按钮存在
                if ($(self).find(".rapidAddImg").length > 0) {
                    // 绑定添加点击事件
                    $(self).find(".rapidAddImg").bind("click", function(e) {
                        $(self).find(".fileImage").click();
                    });
                }
            };
            // 初始化上传控制层插件
            this.init();
        });
    };
})(KindEditor);
KindEditor.plugin('multiimage', function(K) {
    var self = this,
        name = 'multiimage',
        formatUploadUrl = K.undef(self.formatUploadUrl, true),
        uploadJson = K.undef(self.uploadJson, self.basePath + '/Home/Notify/mulUploadImg'),
        imgPath = self.pluginsPath + 'multiimage/images/',
        imageSizeLimit = K.undef(self.imageSizeLimit, '1MB'), //最大上传容量
        imageFileTypes = K.undef(self.imageFileTypes, '*.jpg;*.gif;*.png'), //图片格式/类型
        imageUploadLimit = K.undef(self.imageUploadLimit, 10), //每次最多上传20张
        filePostName = K.undef(self.filePostName, 'imgFile'),
        lang = self.lang(name + '.');
    self.plugin.multiImageDialog = function(options) {
        var clickFn = options.clickFn,
            uploadDesc = K.tmpl(lang.uploadDesc, {
                uploadLimit: imageUploadLimit,
                sizeLimit: imageSizeLimit
            });
        var html = [
            '<div style="padding:20px;">',
            '<div class="swfupload">',
            '</div>',
            '</div>'
        ].join('');
        var dialog = self.createDialog({
                name: name,
                width: 650,
                height: 510,
                title: self.lang(name),
                body: html,
                previewBtn: {
                    name: lang.insertAll,
                    click: function(e) {

                        var urlList = [];
                        $.each(uploadEle.data('uploadList'), function(k, v) {
                            urlList.push({
                                url: v.res.url,
                                title: v.name,
                            });
                        })
                        clickFn.call(self, urlList);
                    }
                },
                yesBtn: {
                    name: lang.clearAll,
                    click: function(e) {
                        uploadEle.find('.file_del').click();
                    }
                },
                beforeRemove: function() {}
            }),
            div = dialog.div,
            uploadEle = $(div[0]).find('.swfupload').zyUpload({
                itemWidth: "120px", // 文件项的宽度
                itemHeight: "80px", // 文件项的高度
                url: uploadJson, // 上传文件的路径
                multiple: true, // 是否可以多个文件上传
                dragDrop: false, // 是否可以拖动上传文件
                del: true, // 是否可以删除文件
                finishDel: false, // 是否在上传文件完成后删除预览
                uploadDesc: uploadDesc,
                startButtonValue: lang.startUpload,
                filePostName: filePostName,
                /* 外部获得的回调接口 */
                onSelect: function(files, allFiles) { // 选择文件的回调方法
                },
                onDelete: function(file, surplusFiles) { // 删除一个文件的回调方法
                },
                onSuccess: function(file, res) { // 文件上传成功的回调方法
                    //zyUpload
                },
                onFailure: function(file) { // 文件上传失败的回调方法
                },
                onComplete: function(responseInfo) { // 上传完成的回调方法
                }
            });
        return dialog;
    };
    self.clickToolbar(name, function() {
        self.plugin.multiImageDialog({
            clickFn: function(urlList) {
                if (urlList.length === 0) {
                    return;
                }
                K.each(urlList, function(i, data) {
                    if (self.afterUpload) {
                        self.afterUpload.call(self, data.url, data, 'multiimage');
                    }
                    self.exec('insertimage', data.url, data.title, data.width, data.height, data.border, data.align);
                });
                // Bugfix: [Firefox] 上传图片后，总是出现正在加载的样式，需要延迟执行hideDialog
                setTimeout(function() {
                    self.hideDialog().focus();
                }, 1000); //
            }
        });
    });
});

//add 20210920
//延时是为了减少图片上传添加失败的概率
function confirm_upload() {
    $(".fileSubmit").css({
        "color": "#000",
    });

    setTimeout(function() {
        $('.fileSubmit').text('正在上传中');
        $('.file_dig').css({
            "display": "block",
        });
        //alert('正在上传中');
    }, 500);

    setTimeout(function() {
        $('.file_dig').text('注意：不要上传空数据');
    }, 1500);

    setTimeout(function() {
        $('.file_dig').text('建议上传的单张图片控制在300KB之内');
    }, 2000);

    setTimeout(function() {
        $('.file_dig').text('上传成功 [请点击 全部插入 图片！！！]');
    }, 3000);

    setTimeout(function() {
        $('.file_dig').css({
            "display": "none",
        });
        "$('.fileSubmit').text('上传成功').css({ 'color' : 'red', })";
        $('.file_dig').text('正在上传中！！！');
        $('.fileSubmit').text('确认上传文件').css({
            'color': 'red',
        });
    }, 4000);

    //var t1=setTimeout("alert('图片正在上传中...')",2500);
    //var t2=setTimeout("alert('图片已成功上传！')",5000);
    //var t3=setTimeout("alert('请点击 全部插入 才有效！')",7000);
}
