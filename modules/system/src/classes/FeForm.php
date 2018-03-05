<?php namespace System\Classes;


use Collective\Html\FormBuilder;

class FeForm extends FormBuilder
{

	/**
	 * 上传缩略图
	 * @param       $name
	 * @param null  $value
	 * @param array $options
	 * @return string
	 */
	public function webUploader($name, $value = null, $options = [])
	{
		if (!isset($options['name'])) $options['name'] = $name;

		$options['id']          = $this->getIdAttribute($name, $options);
		$value                  = (string) $this->getValueAttribute($name, $value);
		$options['interactive'] = isset($options['interactive']) && $options['interactive'] ? true : false;

		$id = str_random(4);

		$thumb_key   = $value ?: '';
		$display_str = !$value ? "class=\"hidden icons\"" : "class=\"icons\"";
		$extension   = $options['ext'] ?? 'zip';
		$upload_url  = $options['upload_url'] ?? '';
		$field       = $options['field'] ?? 'uploader_file';
		$desc        = Uploader::type($extension, 'desc');
		$extensions  = Uploader::type($extension, 'ext_string');
		$fileSize    = 20 * 1024 . 'Kb';
		if ($extension == 'image') {
			$iconStr = '';
			if ($value) {
				$iconStr = '<img src="' . $value . '">';
			}
			$fileSize = '500Kb';
		}
		else {
			$iconStr = '<i id="web_uploader_preview_' . $id . '" class="fa fa-file-archive-o fa-lg"></i>';
		}

		$parseStr = <<<CONTENT
		<div id="uploader_{$id}" class="sj--webuploader clearfix sj--webuploader-{$extension}">
		    <input type="hidden" name="{$name}" value="{$thumb_key}" id="web_uploader_url_{$id}"/>
		    <div id="thelist_{$id}" class="uploader-list"></div>
		    <div class="clearfix">
			     <div class="btns">
			          <div id="picker_{$id}">{$desc}</div>
			     </div>
			     <span id="web_uploader_preview_{$id}_ctr" {$display_str} class="">
					  {$iconStr}
					  <i id='web_uploader_img_del_{$id}' class="glyphicon glyphicon-remove fa-lg"></i>
				 </span>
		    </div>
		    <div class="statusBar">
		        <div class="progress">
		            <span><span>
		        </div>
		        <div class="info"></div>
		    </div>
		</div>
		<script>
			require(['jquery','jquery.webuploader', 'global'], function($, WebUploader, poppy){
				var extension = '{$extension}';
				var uploader = WebUploader.create({
					dnd : '#uploader_{$id}',
					swf : poppy.url_js + '/libs/jquery.webuploader/0.1.5/Uploader.swf',  // swf文件路径
					server: '{$upload_url}',   // 文件接收服务端
					auto  : true,
					accept : {
					    title: '{$desc}',
					    extensions: '{$extensions}'
					},
					formData : {
					    'ext' : '{$extension}'
					},
					fileNumLimit: 1,
					fileSingleSizeLimit: '{$fileSize}',
					// fileSizeLimit: '{$fileSize}',
					fileVal: '{$field}',
					pick: '#picker_{$id}'   // 选择文件的按钮。可选
					
				});
				var \$wrap = $('#uploader_{$id}');
				var \$queue = $('<ul class="filelist"></ul>')
                    .appendTo(\$wrap.find('.queueList'));
                var  percentages = {};
                var \$statusBar = \$wrap.find('.statusBar');
                var \$progress = \$statusBar.find('.progress').hide();
                var state = 'pedding';
                var fileCount = 0;
                var fileSize = 0;
                var \$info = \$statusBar.find('.info').hide();
				uploader.onFileQueued = function (file) {
					addFile(file);
				};
				uploader.onUploadProgress = function (file, percentage) {
					var \$li = $('#' + file.id),
					    \$percent = \$li.find('.progress span');
					
					\$percent.css('width', percentage * 100 + '%');
					percentages[file.id][1] = percentage;
					updateTotalProgress();
				};
				uploader.onUploadSuccess = function(file, obj_resp){
					if (obj_resp.status == 'error') {
					    alert(obj_resp.msg);
					} else {
						if (extension == 'image') {
							var \$img = $('<img >');
							\$img.prop('src', obj_resp.data.url);
							\$img.prop('class', 'J_image_preview');
							$("#web_uploader_preview_{$id}_ctr").append(\$img).removeClass('hidden').show();
							$("#web_uploader_preview_{$id}").css('display', 'none');
							$('#web_uploader_url_{$id}').val(obj_resp.data.url);
						} else {
							$('#web_uploader_url_{$id}').val(obj_resp.data.destination);
						    $('#web_uploader_preview_{$id}_ctr').removeClass('hidden');
						}
					}
					$("#web_uploader_preview_{$id}_ctr").show();
				}
				
				// 当有文件添加进来时执行，负责view的创建
		        function addFile(file) {
		            var \$li = $('<li id="' + file.id + '">' +
		                    '<p class="title">' + file.name + '</p>' +
		                    '<p class="progress"><span></span></p>' +
		                    '</li>'),
		
		                \$prgress = \$li.find('p.progress span'),
		                \$info = $('<p class="error"></p>'),
		
		                showError = function (code) {
		                    switch (code) {
		                        case 'exceed_size':
		                            text = '文件大小超出';
		                            break;
		
		                        case 'interrupt':
		                            text = '上传暂停';
		                            break;
		
		                        default:
		                            text = '上传失败，请重试';
		                            break;
		                    }
		
		                    \$info.text(text).appendTo(\$li);
		                };
		
		            if (file.getStatus() === 'invalid') {
		                showError(file.statusText);
		            } else {
		                // @todo lazyload
		                percentages[file.id] = [file.size, 0];
		                file.rotation = 0;
		            }
		
		            file.on('statuschange', function (cur, prev) {
		                if (prev === 'progress') {
		                    \$prgress.hide().width(0);
		                } else if (prev === 'queued') {
		                    \$li.off('mouseenter mouseleave');
		                }
		
		                // 成功
		                if (cur === 'error' || cur === 'invalid') {
		                    console.log(file.statusText);
		                    showError(file.statusText);
		                    percentages[file.id][1] = 1;
		                } else if (cur === 'interrupt') {
		                    showError('interrupt');
		                } else if (cur === 'queued') {
		                    percentages[file.id][1] = 0;
		                } else if (cur === 'progress') {
		                    \$info.remove();
		                    \$prgress.css('display', 'block');
		                } else if (cur === 'complete') {
		                    \$li.append('<span class="success"></span>');
		                }
		
		                \$li.removeClass('state-' + prev).addClass('state-' + cur);
		            });
		
		            \$li.appendTo(\$queue);
		        }
		        function updateTotalProgress() {
		            if( state == 'uploading') {
		                \$progress.show();
		            }
		            var loaded = 0,
		                total = 0,
		                spans = \$progress.children(),
		                percent;
		
		            $.each(percentages, function (k, v) {
		                total += v[0];
		                loaded += v[0] * v[1];
		            });
		            percent = total ? loaded / total : 0;
		
		            spans.eq(0).text(Math.round(percent * 100) + '%');
		            spans.eq(0).css('width', Math.round(percent * 100) + '%');
		            updateStatus();
		        }
		        
		        function updateStatus() {
		            var text = '', stats;
		
		            if (state === 'ready') {
		                // 
		            } else if (state === 'confirm') {
		                //
		
		            }else if (state === 'pedding') {
		                //
		
		            } else {
		                stats = uploader.getStats();
		                text = '共' + fileCount + '个（' +
		                    WebUploader.formatSize(fileSize) +
		                    '），已上传' + stats.successNum + '个';
		
		                if (stats.uploadFailNum) {
		                    text += '，失败' + stats.uploadFailNum + '个';
		                }
		                \$info.html(text).show();
		            }
		            
		        }
				function setState(val) {
		            var file, stats;
		
		            if (val === state) {
		                return;
		            }
		
		            state = val;
		
		            switch (state) {
		                case 'pedding':
		                    \$queue.parent().removeClass('filled');
		                    \$queue.hide();
		                    \$statusBar.addClass('element-invisible');
		                    uploader.refresh();
		                    break;
		
		                case 'ready':
		                    $('#filePicker2').removeClass('element-invisible');
		                    \$queue.parent().addClass('filled');
		                    \$queue.show();
		                    \$statusBar.removeClass('element-invisible');
		                    uploader.refresh();
		                    break;
		
		                case 'uploading':
		                    $('#filePicker2').addClass('element-invisible');
		                    \$progress.show();
		                    break;
		
		                case 'paused':
		                    \$progress.show();
		                    break;
		
		                case 'confirm':
		                    \$progress.hide();
		
		                    stats = uploader.getStats();
		                    if (stats.successNum && !stats.uploadFailNum) {
		                        setState('finish');
		                        return;
		                    }
		                    break;
		                case 'finish':
		                    stats = uploader.getStats();
		                    if (stats.successNum) {
		                        // alert('上传成功');
		                    } else {
		                        // 没有成功的图片，重设
		                        state = 'done';
		                        location.reload();
		                    }
		                    break;
		            }
		
		            updateStatus();
		        }
		
		        uploader.onUploadProgress = function (file, percentage) {
		            var \$li = $('#' + file.id),
		                \$percent = \$li.find('.progress span');
		
		            \$percent.css('width', percentage * 100 + '%');
		            percentages[file.id][1] = percentage;
		            updateTotalProgress();
		        };
		
		        uploader.onFileQueued = function (file) {
		            fileCount++;
		            fileSize += file.size;
		
		            if (fileCount === 1) {
		                \$statusBar.show();
		            }
		
		            addFile(file);
		            setState('ready');
		            updateTotalProgress();
		        };
		
		        uploader.onFileDequeued = function (file) {
		            fileCount--;
		            fileSize -= file.size;
		
		            if (!fileCount) {
		                setState('pedding');
		            }
		
		            removeFile(file);
		            updateTotalProgress();
		
		        };
		
		        uploader.on('all', function (type) {
		            var stats;
		            switch (type) {
		                case 'uploadFinished':
		                    setState('confirm');
		                    break;
		
		                case 'startUpload':
		                    setState('uploading');
		                    break;
		
		                case 'stopUpload':
		                    setState('paused');
		                    break;
		
		            }
		        });
		
		        uploader.onError = function (code) {
		            if (code == 'Q_EXCEED_SIZE_LIMIT') {
		                alert('文件大小超出限制' + '{$fileSize}')
		            } else {
		                alert('Error: ' + code);
		            }
		           
		        };
		
		
		        \$info.on('click', '.retry', function () {
		            uploader.retry();
		        });
		
		        \$info.on('click', '.ignore', function () {
		            alert('todo');
		        });
		
		        updateTotalProgress();
				
				$("#web_uploader_img_del_{$id}").click(function () {
					$("#web_uploader_preview_{$id}_ctr").hide();
					$("input[name={$name}]").val('');
					uploader.reset();
					\$info.hide();
				});
			})
		</script>
CONTENT;
		return $parseStr;
	}
}