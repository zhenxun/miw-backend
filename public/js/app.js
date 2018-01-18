$(document).ready(function(){

	var hostname ='http:\\\\' + window.location.hostname;
	var pathname = window.location.pathname;
	var start = 1;
	var get_grid_nums = $(".nums-upload option:selected").val();
	var default_Photos = ['nia1.png','nia3-500.png'];
	var explode_pathname = pathname.split('/');
	var num_explode_pathname = parseInt(explode_pathname.length)-1;
	var urls = hostname +'/cover/' + explode_pathname[num_explode_pathname];

	//$('img').wrap('<span style="display:inline-block"></span>').css('display', 'block').parent().zoom({on:'mouseover'});
	
	$('#contactTable').DataTable();
	
	$('.btn-home-upload, .thumb_click').click(function(e){
		e.preventDefault();
		var currentPlace = $(this).attr('role');
		$('input.current-place').val(currentPlace);
		$('.modal-upload').modal('show');
	});


	function producedGrid(hostname, grid_nums, position, default_photo, save_photo, save_photo_num){

		console.log(save_photo_num);

		if(save_photo_num == 0)
		{
			for(a = 1; a <= grid_nums; a++)
			{
				$('.content-upload-grid').append(
	            '<div class="col-sm-6 col-md-4">'+
	                '<div class="thumbnail">'+
	                    '<img src="'+ hostname + '/img/'+ default_photo + '" style="padding-top:10px;">'+
	                        '<div class="caption" style="margin-top:-60px;">'+
	                            '<button class="btn btn-outline btn-warning  dim cover-upload" role="'+ a +'" position="'+ position +'"  type="button"><i class="fa fa-upload"></i></button>'+
	                        '</div>'+
	                '</div>'+
	            '</div>	'			
				);	
			}			
		}

		if(save_photo_num <= grid_nums && save_photo_num != 0)
		{
			var makeStart = 1 + parseInt(save_photo_num);

			console.log(makeStart);

			for(a = 0; a < save_photo_num; a++)
			{	
				if(save_photo[a]['filename'] != null)
				{
					$('.content-upload-grid').append(
						'<div class="col-sm-6 col-md-4">'+
							'<div class="thumbnail">'+
								'<img src="'+ hostname + '/upload/covers/'+ save_photo[a]['filename'] + '" style="padding-top:10px;width:494px;height:376px">'+
									'<div class="caption" style="margin-top:-60px;">'+
										'<button class="btn btn-outline btn-warning  dim cover-upload" role-id="'+ save_photo[a]['id'] +'" role="'+ (a+1) +'" position="'+ position +'"  type="button"><i class="fa fa-upload"></i></button>'+
									'</div>'+
							'</div>'+
						'</div>	'			
					);
				}
				else
				{
					$('.content-upload-grid').append(
						'<div class="col-sm-6 col-md-4">'+
							'<div class="thumbnail">'+
								'<img src="'+ hostname + '/img/'+ default_photo + '" style="padding-top:10px;width:494px;height:376px">'+
									'<div class="caption" style="margin-top:-60px;">'+
										'<button class="btn btn-outline btn-warning  dim cover-upload"  role="'+ (a+1) +'" position="'+ position +'"  type="button"><i class="fa fa-upload"></i></button>'+
									'</div>'+
							'</div>'+
						'</div>	'			
					);					
				}
	
			}

			for(b = makeStart; b<=grid_nums; b++)
			{
				$('.content-upload-grid').append(
		            '<div class="col-sm-6 col-md-4">'+
		                '<div class="thumbnail">'+
		                    '<img src="'+ hostname + '/img/'+ default_photo + '" style="padding-top:10px;width:494px;height:376px">'+
		                        '<div class="caption" style="margin-top:-60px;">'+
		                            '<button class="btn btn-outline btn-warning  dim cover-upload"  role="'+ b +'" position="'+ position +'"  type="button"><i class="fa fa-upload"></i></button>'+
		                        '</div>'+
		                '</div>'+
		            '</div>	'			
				);

			}
		}
	}

	var renderData = function(get_grid_nums){

		$.ajax({
			url: urls,
			type:'GET',
			error: function(xhr){
				console.log('資料請求錯誤!');
			},
			success: function(response){
				console.log(response);
				var obj = jQuery.parseJSON(response);
				var position = explode_pathname[num_explode_pathname]; 
				if(obj != 'nl'){

					console.log(obj);
					
					if(get_grid_nums == obj.length)
					{
						producedGrid(hostname,get_grid_nums,position,default_Photos[1],obj,obj.length);
					}
					else if(get_grid_nums < obj.length)
					{
						var real_obj_length = obj.length - (obj.length - get_grid_nums); 
						if(confirm("當前所選格數少於原有儲存格數(" + obj.length + "格),是否確定減少原有格數?"))
						{
							for(var a = get_grid_nums; a <= obj.length-1; a++){
								if( obj[a]['id'] != '')
								{
									var uri = hostname + "/cover/" + obj[a]['id'];
									$.ajax({
										url:uri,
										type:'DELETE',
										success: function(data){
											if(data == '1')
											{
												location.reload();
											}
										}
									});
								}
							}
							producedGrid(hostname,get_grid_nums,position,default_Photos[1],obj,real_obj_length);	
						}
						else
						{
							location.reload();
						}
						
					}
					else
					{
						producedGrid(hostname,get_grid_nums,position,default_Photos[1],obj,obj.length);
					}
				}
				else
				{
					console.log('this if');
					producedGrid(hostname,get_grid_nums,position,default_Photos[1],'',0);
				}

				
			}
		});

	}

	var noRenderPage = ['/','/forgot','/sigin','/attachment','/contact','/setting','/setting/footer'];

	if(jQuery.inArray(pathname, noRenderPage) == -1)
	{
		renderData(get_grid_nums);
	}

	$('.btn-create-thumbnails').click(function(e){
		e.preventDefault();
		var get_grid_nums = $(".nums-upload option:selected").val();
		$('.content-upload-grid').empty();
		renderData(get_grid_nums);

	});

	var furnitureNo = 2;
	$('.model-furniture').on('click','.btn-model-add-file',function(e){
		e.preventDefault();

		$('.fileUpload-content').append(
			'<div class="form-group">'+
                  '<label for="inputEmail3" class="col-sm-2 control-label">'+ furnitureNo +':</label>'+
                  '<div class="col-sm-4">'+
                    '<input type="file" class="form-control" name="furniture[]" id="furniture" >'+
				  '</div>'+
                  '<div class="col-sm-4">'+
                      '<div style="margin-top:5px;">'+
                        '<input type="radio" checked="checked" value="1" name="type'+ furnitureNo +'"> 雜誌內頁 &nbsp;&nbsp;'+
                        '<input type="radio" value="2" name="type'+furnitureNo+'"> 廣告'+
                      '</div>'+
                  '</div> '+
                  '<div class="col-sm-2">'+
                  '<i class="fa fa-trash del-file-input" aria-hidden="true" style="cursor:pointer;"></i>'+
                  '</div>'+
            '</div>'
		);

		furnitureNo = furnitureNo + 1;
	});

	$('div#modal-furniture').on('hidden.bs.modal', function(e){
		e.preventDefault();
		$('.fileUpload-content div.form-group').not(':first').remove();
	});


	$('.topicDelete').click(function(e){
		e.preventDefault();
		var getdelId = $(this).attr('role');

		$("input#save-id").val(getdelId);

		$('#modal-del').modal('show');
	});


	$('.brandDelete').click(function(e){
		e.preventDefault();
		var getdelId = $(this).attr('role');

		$("input#save-id").val(getdelId);

		$('#modal-del').modal('show');
	});


	$('.wanted-delete-topic-content').click(function(e){
		e.preventDefault();
		var getid = $("input#save-id").val();
		var uri = hostname + "/content/"+ getid;

			$.ajax({
			  url: uri,
			  type: 'DELETE',
			  success: function(data) {
			     if(data == '1')
			     {
			     	alert('資料已成功刪除!');
			     	location.reload();
			     }
			     else
			     {
			     	alert('資料已刪除失敗，請重新嘗試!');
			     	location.reload();			     	
			     }
			  }
			});
	});


	$('.wanted-delete-brand-content').click(function(e){
		e.preventDefault();
		var getid = $("input#save-id").val();
		var uri = hostname + "/content/"+ getid;

			$.ajax({
			  url: uri,
			  type: 'DELETE',
			  success: function(data) {
			     if(data == '1')
			     {
			     	alert('資料已成功刪除!');
			     	location.reload();
			     }
			     else
			     {
			     	alert('資料已刪除失敗，請重新嘗試!');
			     	location.reload();			     	
			     }
			  }
			});
	});


	$('.fileUpload-content').on('click','.del-file-input',function(e){
		e.preventDefault();
		var getThisFileInput = $(this).parent().parent();
		getThisFileInput.remove();
		furnitureNo = furnitureNo - 1;
	});


	$("form#addFurnitureForm").validate({
		errorLabelContainer: $("form#addFurnitureForm div.error"),
		wrapper: 'li',
		rules:{
			'furniture[]':{
				required: true,
				extension: "jpg|jpeg|png|svg|gif"
			}
		},
		messages:{
			'furniture[]':{
				required:"內容文件不能空白",
				extension: "文件僅接受以下格式 jpg,jpeg,png,svg,gif"				
			}
		}
	});


	$('.btnExecuteFurnitureUpload').click(function(e){
		e.preventDefault();
		console.log(e);
		$('.btnUploadFurniture').click();
		//var file = $("input[name='']")[0].files[0];

	});

	$('.contact-del').click(function(e){
		e.preventDefault();
		var getdelId = $(this).attr('role'); 

		$("input#save-id").val(getdelId);

		$('#modal-del').modal('show');

	});


	$('.wanted-delete-contact').click(function(e){
		e.preventDefault();
		var getId = $('input#save-id').val();

		var url = "contact/"+ getId;

			$.ajax({
			  url: url,
			  type: 'DELETE',
			  success: function(data) {
			     if(data == '1')
			     {
			     	alert('資料已刪除成功!');
			     	 location.reload();
			     }
			     else
			     {
			     	alert('資料已刪除失敗，請重新嘗試!');
			     	location.reload();			     	
			     }
			  }
			});

	});



	$('form#changePasswordForm').validate({
		errorLabelContainer: $("form#changePasswordForm div.error"),
		wrapper: 'li',
		rules:{
			newpwd:{
				required: true,
				minlength: 5
			},
			confirmpwd:{
				required: true,
				minlength: 5,
				equalTo: "#new-password"
			}
		},
		messages:{
			newpwd:{
				required: "新密碼欄位不能空白",
				minlength: "密碼不能少過5個字元"
			},
			confirmpwd:{
				required: "確認密碼欄位不能空白",
				minlength: "密碼不能少過5個字元",
				equalTo: "確認密碼與新密碼不符"
			}
		}
	});


	$('input[name="chn_file"],input[name="eng_file"]').change(function(e){
		e.preventDefault();
		var fileExtension = ['pdf'];
		if($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == 0)
		{

				$(".language").val($(this).attr('role'));

				$('.submitSubscribeForm').click();
				$(".processing-bar-"+$(this).attr('role')).html('<img src="img/icon/loading.svg" style="width:20px;height:20px;"> <strong>文件上傳中</strong>');
				$(".processing-bar-"+$(this).attr('role')).show();
				$("img."+$(this).attr('role')+"AttachType").attr("src","img/icon/pdf.png");
		}
		else
		{
			$('.attach-message').empty();
			$('.attach-message').append('<i class="fa fa-exclamation" aria-hidden="true"></i> 文件格式僅接受pdf.');
			$('.attach-message').show('blind').delay(2000).hide('blind');
		}
		
	});


	$('.btn-attachment-list').click(function(e){
		e.preventDefault();
		var url = 'attachment/search';
		var num = 1;


		$.ajax({
			  url: url,
			  type: 'GET',
			  success: function(data) {
			  	var obj = jQuery.parseJSON(data);

				$("div#modal-list div.modal-body").html(
			  	 	'<table class="table subscribeList">'+
			  	 		'<tr style="background:#5DADE2;color:white;">'+
			  	 			'<td width="10">序</td>'+
			  	 			'<td>類別</td>'+
			  	 			'<td>檔案</td>'+
			  	 			'<td>創建日期</td>'+
			  	 			'<td align="center">編輯</td>'+
			  	 		'</tr>'
			  	 );

				if(obj.length != 0)
				{

					  	$.each(obj, function(key, val){
					  		var type = (val.language == 0)? '中文版訂閱表格':'英文版訂閱表格';
					  		$("table.subscribeList").append(
					  	 		'<tr>'+
					  	 			'<td>'+ parseInt(num) +'</td>'+
					  	 			'<td>'+type+'</td>'+
					  	 			'<td>'+val.filename+'</td>'+
					  	 			'<td>'+val.created_at+'</td>'+
					  	 			'<td align="center"><a title="刪除" role="'+ val.id +'" class="subscribe-delete"><i class="fa fa-trash" aria-hidden="true" style="cursor: pointer;"></i></a></td>'+
					  	 		'</tr>'+
					  	 		'</table>'			  			
					  		);

					  		num = num + 1;
					  	});
			  	}
			  	else
			  	{
			  		$("table.subscribeList").append('<tr><td class="text-center" colspan="5">---目前尚無資料記錄---</td></tr>');
			  	}

			  	

			  }
		});


		$('div#modal-list').modal('show');

	});

	$("div#modal-list div.modal-body").on('click','.subscribe-delete', function(e){
		e.preventDefault();
		var getdelId = $(this).attr('role'); 
		$("input#save-id").val(getdelId);		
		$('#modal-del').modal('show');
	});

	$(".wanted-delete-subscribe").click(function(e){
		e.preventDefault();
		var getId = $('input#save-id').val();

		var url = "attachment/"+ getId;

			$.ajax({
			  url: url,
			  type: 'DELETE',
			  success: function(data) {
			     if(data == '1')
			     {
			     	 alert('資料已刪除成功');
			     	 // $('#modal-del').modal('hide');
			     	 $('.btn-attachment-list').click();
			     }
			     else
			     {
			     	alert('資料已刪除失敗，請重新嘗試!');
			     	location.reload();			     	
			     }

			  }
			});
	});


	$(".home-upload").click(function(e){
		e.preventDefault();
		var position = $('.position').val();
		var files = [];
		var isEmpty = [];
		var check = [];
		var fileExtension = ['jpg','jpeg','png','svg','bmp'];

		$('input[type="file"]').each(function(){
			if($(this).val() != "")
			{
				files.push($(this).val());
			}
			else
			{
				isEmpty.push(1);
			}

			if($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) > -1)
			{check.push(0);}
			else
			{check.push(-1);}
		});

		if(files.length > 0)
		{
			if( check[0] == 0 && check[1] == 0 && isEmpty[0]!=1 && isEmpty[1]!=1 )
			{
				$('.home-do-upload').click();
			}
			else if(isEmpty[0]!=1 && isEmpty[1]!=1)
			{
				$('.home-message').html('<i class="fa fa-times" aria-hidden="true"></i> 僅提供jpg, jpeg, png, svg, bmp格式!');
				$('.home-message').show('blind').delay(2000).hide('blind');				
			}
			else
			{
				$('.home-message').html('<i class="fa fa-times" aria-hidden="true"></i> 必須上傳兩個檔案!');
				$('.home-message').show('blind').delay(2000).hide('blind');				
			}
		}
		else
		{
			$('.home-message').html('<i class="fa fa-times" aria-hidden="true"></i> 文件欄位不能空白!');
			$('.home-message').show('blind').delay(2000).hide('blind');
		}

	});


	$(".btn-home-list").click(function(e){
		e.preventDefault();
		var url = 'search';

		$.ajax({
			  url: url,
			  type: 'GET',
			  success: function(data) {
			  	var obj = jQuery.parseJSON(data);

				$("div#modal-list div.modal-body").html(
			  	 	'<table class="table indexList">'+
			  	 		'<tr style="background:#5DADE2;color:white;">'+
			  	 			'<td width="10">序</td>'+
			  	 			'<td>檔案名稱</td>'+
			  	 			'<td>位置</td>'+
			  	 			'<td>創建日期</td>'+
			  	 			'<td align="center">編輯</td>'+
			  	 		'</tr>'
			  	 );	


				if(obj.length > 0)
				{

					$.each(obj, function(key, val){
						var type = (val.position == 0)? '首頁':'未知';

						$('table.indexList').append(
							'<tr>'+
								'<td>'+val.rank+'</td>'+
								'<td><a target="_blank" href="../upload/covers/'+val.filename+'">'+val.filename+'</a></td>'+
								'<td>'+ type +'</td>'+
								'<td>'+val. created_at+'</td>'+
								'<td class="text-center"><a title="刪除" role="'+ val.id +'" class="index-delete"><i class="fa fa-trash" aria-hidden="true" style="cursor: pointer;"></i></a></td>'+
							'</tr>'
						);

					});	
				}
				else
				{
					$("table.indexList").append('<tr><td class="text-center" colspan="5">---目前尚無資料記錄---</td></tr>');
				}	  	

			  }
		});		

		$("div#modal-list").modal('show');
	});


	$("div#modal-list div.modal-body").on('click','.index-delete',function(e){
		e.preventDefault();
		var getid = $(this).attr('role');
		$("input#save-id").val(getid);		
		$('#modal-del').modal('show');
	});


	$('.wanted-delete-index').click(function(e){
		e.preventDefault();
		var getid = $('input#save-id').val();

		var uri = "search/"+ getid;

			$.ajax({
			  url: uri,
			  type: 'DELETE',
			  success: function(data) {
			     if(data == '1')
			     {
			     	 $('#modal-del').modal('hide');
			     	 $("div#modal-list").modal('hide');
			     	 $('#modalMessage .modal-body').html('<span style="color:#229954;font-size:25px;padding-top:10px;"> <i class="fa fa-check" aria-hidden="true"></i> <strong>內容刪除成功!</strong></span>');
			     	 $('#modalMessage').modal('show');

			     }
			  }
			});	
	});


	$("div.content-upload-grid").on('click','button.cover-upload',function(e){
		e.preventDefault();
		var thisRole = $(this).attr('role');
		var thisRoleId = $(this).attr('role-id');
		var thisPosition = $(this).attr('position');
		var x;

		var uri = hostname + '/cover/check/' + thisPosition + '/' + thisRoleId;

		$.ajax({
			url: uri,
			type:'GET',
			error: function(xhr){
				console.log('Ajax request error');
			},
			success: function(response){
				if(response != 'null')
				{var obj = jQuery.parseJSON(response);}
				else
				{ x = true;};

				if(x)
				{
					$('label#coverFile').html('第 '+ thisRole +' 格封面上傳:');
					$('input[name="rank"]').val(thisRole);
					$('input[name="position"]').val(thisPosition);

					start = 1;

					$("table.containerFile tr").not(':first').remove();

					$('.modal-upload').modal('show');
				}
				else
				{
					$('input[id="add-content-id"]').val(obj['id'])
					$('input[name="coverid"]').val(obj['id']);
					$('input[name="position"]').val(thisPosition);
					$('.cover-check-content-list').attr('role',obj['id']);
					$("#modal-cover-check").modal('show');
				}

			}
		});		

	});


	$(".add-content").click(function(e){
		e.preventDefault();
		$("#modal-cover-check").modal('show');
		$("#modal-upload-content").modal('show');
	});

	var start;
	$("button.btn-file-add").click(function(e){
		e.preventDefault();
		start = parseInt(start) + 1;
		$("table.containerFile").append(
			'<tr>'+
                '<td>'+ start +'.</td>'+
				'<td><input type="file" name="contentFile[]" id="contentFile"></td>'+
				'<td>'+
					'<input type="radio" checked="checked" value="1" name="type'+ start +'"> 雜誌內頁 &nbsp;&nbsp;'+
					'<input type="radio" value="2" name="type'+ start +'"> 廣告' +
				'</td>' +
                '<td><a class="del-add-file" title="刪除" style="cursor:pointer;"><i class="fa fa-minus" aria-hidden="true" role="'+ start +'"></i></a></td>'+
            '</tr>' 
		);
	});


	$("button.btn-file-add-content").click(function(e){
		e.preventDefault();
		start = parseInt(start) + 1;
		$("table.containerFile").append(
			'<tr>'+
                '<td>'+ start +'.</td>'+
				'<td><input type="file" name="contentFile[]" id="contentFile"></td>'+
				'<td>'+
					'<input type="radio" checked="checked" value="1" name="type'+ start +'"> 雜誌內頁 &nbsp;&nbsp;'+
					'<input type="radio" value="2" name="type'+ start +'"> 廣告' +
				'</td>' +				
                '<td><a class="del-add-file" title="刪除" style="cursor:pointer;"><i class="fa fa-minus" aria-hidden="true" role="'+ start +'"></i></a></td>'+
            '</tr>' 
		);		
	});


	$("table.containerFile").on('click','.del-add-file',function(e){
		var getThis = $(this).parent().parent();
		start = start - 1;
		getThis.remove();
	});

	$('.btn-files-list').click(function(e){
		e.preventDefault();
		var position = $(this).attr('position');
		$("div#modal-list .modal-body").empty();
		
		$.ajax({
			url: urls,
			type:'GET',
			error: function(xhr){
				console.log('Ajax request error');
			},
			success: function(response){
				var obj = jQuery.parseJSON(response);

				$("div#modal-list .modal-body").append('<table class="table table-bordered cover-table-list">'+
						'<tr style="background:#21618C;color:white;">'+
							'<td align="center" width="50">格數</td>'+
							'<td align="center">原始檔名</td>'+
							'<td>封面檔案</td>'+
							'<td width="150" align="center">功能</td>'+
						'</tr>'
				);

				if(obj.length > 0 )
				{
					for(var a = 0; a< obj.length; a++)
					{
						if(obj[a]['filename'] != null && obj[a]['filename'] != 'null' && obj[a]['filename'] != '')
						{
							var ori_filename = (obj[a]['ori_filename']!= null && obj[a]['ori_filename']!= '')? obj[a]['ori_filename']:'';
							$("table.cover-table-list").append(
								'<tr>'+
									'<td align="center">'+ (a+1) +'</td>'+
									'<td>'+ ori_filename +'</td>'+
									'<td>'+ obj[a]['filename'] +'</td>'+
									'<td>'+
									'<button type="button" role="'+ obj[a]['id'] +'" class="btn btn-xs btn-info open-content-list" style="color:#F0F3F4;">檢視封面內容</button> &nbsp;'+
									'<button type="button" role="'+ obj[a]['id'] +'"class="btn btn-xs btn-danger cover-delete" style="color:#F0F3F4;">刪除</button>'+
									'</td>'+
								'</tr>'
							);
						}	
					}
				}
				else
				{
					$("table.cover-table-list").append('<tr><td class="text-center" colspan="3">---目前尚無資料記錄---</td></tr>');
				}

				$("div#modal-list .modal-body").append('</table>');
			}
		});	

		$("div#modal-list").modal('show');
		$('.content-upload-grid').empty();
		renderData();
	});


	$("div#modal-list").on('click','.cover-delete',function(e){
		e.preventDefault();
		var getid = $(this).attr('role');
		$("input#save-id").val(getid);
		$("div#modal-cover-del").modal('show');
	});

	$('.cover-check-content-list').click(function(e){
		e.preventDefault();
		var getid = $(this).attr('role');
		$("div#modal-content-list .modal-body").empty();

		var urls = hostname + '/content/' + getid;
		
		$.ajax({
			url: urls,
			type:'GET',
			error: function(xhr){
				console.log('Ajax request error');
			},
			success: function(response){
				var obj = jQuery.parseJSON(response);

				$("div#modal-content-list .modal-body").append('<table class="table table-bordered content-table-list">'+
						'<tr style="background:#21618C;color:white;">'+
							'<td width="10">序</td>'+
							'<td>原始檔名</td>'+
							'<td width="100">內容類別</td>'+
							'<td width="150" align="center">功能</td>'+
						'</tr>'
				);

				for(var b = 0; b < obj.length; b++)
				{
					var content_type = (obj[b]['type'] == '1')? '雜誌內頁':'廣告';

					$("table.content-table-list").append(
						'<tr>'+
							'<td>'+ obj[b]['rank'] +'</td>'+
							'<td>'+ obj[b]['ori_filename'] +'</td>'+
							'<td>'+ content_type +'</td>'+
							'<td>'+
							'<button type="button" role="'+ obj[b]['id'] +'" class="btn btn-xs btn-info add-tag-content" style="color:#F0F3F4;">標籤內容編輯</button> &nbsp;&nbsp;'+
							'<button type="button" role="'+ obj[b]['id'] +'" class="btn btn-xs btn-danger content-delete" style="color:#F0F3F4;">刪除</button>'+
							'</td>'+
						'</tr>'
					);
				}

				$("div#modal-list .modal-body").append('</table>');
			}
		});	

		$("#modal-cover-check").modal('hide');
		$("#modal-cover-check").removeClass('fade');

		setTimeout(function(){ $("div#modal-content-list").modal('show'); }, 500);
	
	});


	$('div#modal-list').on('click','.open-content-list', function(e){
		e.preventDefault();
		var getid = $(this).attr('role');
		$("div#modal-content-list .modal-body").empty();

		var urls = hostname + '/content/' + getid;
		
		$.ajax({
			url: urls,
			type:'GET',
			error: function(xhr){
				console.log('Ajax request error');
			},
			success: function(response){
				var obj = jQuery.parseJSON(response);

				$("div#modal-content-list .modal-body").append('<table class="table table-bordered content-table-list">'+
						'<tr style="background:#21618C;color:white;">'+
							'<td width="10">序</td>'+
							'<td>原始檔名</td>'+
							'<td width="100">內容類別</td>'+
							'<td width="150" align="center">功能</td>'+
						'</tr>'
				);

				for(var b = 0; b < obj.length; b++)
				{
					var content_type = (obj[b]['type'] == '1')? '雜誌內頁':'廣告';

					$("table.content-table-list").append(
						'<tr>'+
							'<td>'+ obj[b]['rank'] +'</td>'+
							'<td>'+ obj[b]['ori_filename'] +'</td>'+
							'<td>'+ content_type +'</td>'+
							'<td>'+
							'<button type="button" role="'+ obj[b]['id'] +'" class="btn btn-xs btn-info add-tag-content" style="color:#F0F3F4;">標籤內容編輯</button> &nbsp;&nbsp;'+
							'<button type="button" role="'+ obj[b]['id'] +'" class="btn btn-xs btn-danger content-delete" style="color:#F0F3F4;">刪除</button>'+
							'</td>'+
						'</tr>'
					);
				}

				$("div#modal-list .modal-body").append('</table>');
			}
		});	

		$("div#modal-content-list").modal('show');	

	});


	$("div#modal-content-list").on('click','.content-delete',function(e){
		e.preventDefault();
		var getid = $(this).attr('role');
		$("input#save-id").val(getid);
		$('#modal-del').modal('show');		
	});

	$(".wanted-cover-delete").click(function(e){
		e.preventDefault();
		var getid = $('input#save-id').val();
		var uri = hostname + "/cover/" + getid;
		$.ajax({
			url:uri,
			type:'DELETE',
			success: function(data){
				if(data == '1')
				{
					$('#modal-cover-del').modal('hide');
					$('.btn-files-list').click();
					location.reload();
				}
				else
				{
					alert('資料已刪除失敗，請重新嘗試');
					location.reload();
				}
			}
		});

	});


	$(".wanted-delete-content").click(function(e){
		e.preventDefault();
		var getid = $('input#save-id').val();

		var uri = hostname + "/content/"+ getid;
			$.ajax({
			  url: uri,
			  type: 'DELETE',
			  success: function(data) {
			     if(data == '1')
			     {
			     	 $('#modal-del').modal('hide');
			     	 $("div#modal-content-list").modal('hide');
			     	 // $('#modalMessage .modal-body').html('<span style="color:#229954;font-size:25px;padding-top:10px;"> <i class="fa fa-check" aria-hidden="true"></i> <strong>內容刪除成功!</strong></span>');
			     	 alert('資料刪除成功');
			     }
			     else
				 {
				 	alert('刪除資料失敗，請重新嘗試');
				 	location.reload();
				 }
			  }
		});	

	});


	$("form#gridUploadForm").validate({
		errorLabelContainer: $("form#gridUploadForm div.error"),
		wrapper: 'li',		
		rules:{
			coverFile:{
				required: true,
				extension: "jpg|jpeg|png|svg|gif"
			}
		},

		messages:{
			coverFile:{
				required:"封面文件不能空白",
				extension: "文件僅接受以下格式 jpg,jpeg,png,svg,gif"
			}
		}

	});


	$("form#addContentForm").validate({
		errorLabelContainer: $("form#addContentForm div.error"),
		wrapper: 'li',
		rules:{
			'contentFile[]':{
				required: true,
				extension: "jpg|jpeg|png|svg|gif"
			}
		},
		messages:{
			'contentFile[]':{
				required:"內容文件不能空白",
				extension: "文件僅接受以下格式 jpg,jpeg,png,svg,gif"				
			}
		}
	});


	$('.btnExecuteFileUploadClose, button.close').click(function(e){
		$('div.error').hide();
	});


	$('.btnExecuteFileUpload').click(function(e){
		e.preventDefault();
		var file = $("input[name='coverFile']")[0].files[0];
		var imgsize = parseInt(file.size)/1048576;
		var maxsize = 10 // Mb

		$('.error').empty();

		if(imgsize > maxsize)
		{
				$('.error').append('<i class="fa fa-times" aria-hidden="true"></i> &nbsp; 檔案容量大小必須小於 &nbsp;' + '<strong>' + maxsize + 'Mb &nbsp; ( 目前檔案容量大小' + imgsize.toFixed(2) + 'Mb）</strong>');
				$('.error').show('blind').delay(3000).hide('blind');
		}
		else
		{				
			img = new Image();
			  var imgwidth = 0;
			  var imgheight = 0;
			  var maxwidth = 360;
			  var maxheight = 240;
			  var minwidth = 500;
			  var minheight = 371;
			  

			  img.src = URL.createObjectURL(file);
			  img.onload = function() {
				imgwidth = this.width;
				imgheight = this.height;

				if(imgwidth == maxwidth && imgheight == maxheight)
				{
					$('.error').append('<i class="fa fa-times" aria-hidden="true"></i> &nbsp; 圖片尺寸大小必須是 &nbsp;' + '<strong>' + maxwidth + "x" + maxheight + '</strong>');
					$('.error').show('blind').delay(3000).hide('blind');
				}
				else
				{
					$('.gridCoverContentFileUpload').click();
				}

			  }
		}
	});


	$('.btnExecuteFileUploadAddContent').click(function(e){
		e.preventDefault();
		$('.gridAddContentFileUpload').click();
	});



	$(".cover-check-delete").click(function(e){
		e.preventDefault();
		var getid = $('#add-content-id').val();
		$("input#save-id").val(getid);
		$("div#modal-cover-check").modal('hide');		
		$("div#modal-cover-del").modal('show');
	});


	$(".detail-cover").click(function(e){
		e.preventDefault();
		$('.content-upload-grid').empty();
		renderData();

		var uri = hostname + "/detail/annual/cover";
		$.ajax({
			url:uri,
			type:'GET',
			success: function(data){
				var obj = jQuery.parseJSON(data);
				console.log(obj);
				var img_uri = "/upload/covers/" + obj.filename;
				$('.preview-box__image').attr("src",img_uri);
			}
		});

		$("div#modal-annual-cover").modal('show');
	});

	function readURL(input) {

		if (input.files && input.files[0]) {
		  var reader = new FileReader();
	  
		  reader.onload = function(e) {
			$('.preview-box__image').attr('src', e.target.result);
		  }
	  
		  reader.readAsDataURL(input.files[0]);
		}
	  }
	  

	$('#annualCover').change(function(e){
		readURL(this);
	});


	$('form#annualCoverUploadForm').validate({
		errorLabelContainer: $("form#annualCoverUploadForm div.error"),
		wrapper: 'li',
		rules:{
			annualCover:{
				required: true,
				extension: "jpg|jpeg|png|svg|gif"
			}
		},

		messages:{
			annualCover:{
				required:"封面文件不能空白",
				extension: "文件僅接受以下格式 jpg,jpeg,png,svg,gif"
			}
		}
	});


	$(".annual-cover-upload").click(function(e){
		e.preventDefault();
		$('.error').empty();
		var file = $("input[name='annualCover']")[0].files[0];
		var imgwidth = 0;
		var imgheight = 0;
		var maxwidth = 250;
		var maxheight = 300;

		if(file != undefined)
		{
			cover = new Image();

			cover.src = URL.createObjectURL(file);

			cover.onload = function(){
				imgwidth = this.width;
				imgheight = this.height;

				if(imgwidth != maxwidth && imgheight != maxheight)
				{
					$('.error').append('<i class="fa fa-times" aria-hidden="true"></i> &nbsp; 檔案尺寸大小必須是 &nbsp;' + '<strong>' + maxwidth + "px x" + maxheight + 'px </strong>');
					$('.error').show('blind').delay(3000).hide('blind');				
				}
				else
				{
					$(".annualCoverUpload").click();
				}
			}
		}
		else
		{
			$('.error').append('<i class="fa fa-times" aria-hidden="true"></i> &nbsp; 檔案上傳錯誤');
			$('.error').show('blind').delay(3000).hide('blind');
			$(".annualCoverUpload").click();			
		}	

	});


	$('form#resetEmailForm').validate({
		errorLabelContainer: $("form#resetEmailForm div.error"),
		wrapper: 'li',
		rules:{
			resetEmail:{
				required: true,
				email: true,
			}
		},
		messages:{
			resetEmail:{
				required: "新密碼欄位不能空白",
				email: "電子郵件格式錯誤"
			}
		}
	});



	$('form#forgotPasswordForm').validate({
		errorLabelContainer: $("form#forgotPasswordForm div.error"),
		wrapper: 'li',
		rules:{
			sendEmail:{
				required: true,
				email: true
			}
		},
		messages:{
			sendEmail:{
				required: "電子郵件不能空白",
				email: "電子郵件格式不正確"
			}
		}
	});

	$("div#modal-content-list").on('click','.add-tag-content',function(e){
		e.preventDefault();
		var getid = $(this).attr('role');
		$("input.content-id").val(getid);
		$('#modal-content-tag').modal('show');		
	});

	$('.ContentTagClose').click(function(e){
		e.preventDefault();
		$('#summernote').summernote('destroy');
		$('#modal-content-tag').modal('hide');	
	});

	$('#modal-content-tag').on('show.bs.modal', function (e) {


		$('#summernote').summernote({
			toolbar: [
				// [groupName, [list of button]]
				['style', ['bold', 'italic', 'underline', 'clear']],
				['font', ['strikethrough', 'superscript', 'subscript']],
				['fontsize', ['fontsize']],
				['color', ['color']],
				['para', ['ul', 'ol', 'paragraph']],
				['height', ['height']],
				['insert',['picture','link','video','table','hr']],
				['mics',['undo','redo','codeview']]
			],
			fontSizes: ['12','13','14','16','18','20','22','24','26','28','36','48','72'],
			dialogsInBody: true,
			height: 500,
			placeholder: '標籤內容編輯處...',
			callbacks:{
				onImageUpload: function(files){}
			}
		});
		
	});	

	$('#modal-content-tag').on('shown.bs.modal', function (e) {
		var getid = $('.content-id').val();
		var urls = hostname + '/content/each/' + getid;

		$('.note-toolbar-wrapper').css("height", "35px");

		$.ajax({
			url: urls,
			type:'GET',
			error: function(xhr){
				console.log('Ajax request error');
			},
			success: function(response){
				console.log(response);
				var obj = jQuery.parseJSON(response);
				console.log(obj);
				if(obj.is_tag === 0){
					// $('#summernote').summernote('code','');
					// $('#summernote').summernote('disable');

					$('input[name="tag-content-onoffswitch"]').prop("checked", false);
					$('.contents_link').val('');
					$('.contents_link').prop('disabled', true);

				}
				else
				{
					// $('#summernote').summernote('enable');
					// $('#summernote').summernote('code','');
					// $('#summernote').summernote('code', obj.tag_content);
					// var markupStr = $('#summernote').summernote('code');
					// $('.contents_tag').text(markupStr);

					$('input[name="tag-content-onoffswitch"]').prop("checked", true);
					$('.contents_link').prop("disabled", false);
					$('.contents_link').val(obj.tag_content);


				}
			}
		});


	});	

	// $('#summernote').on('summernote.change', function(we, contents, $editable) {
	// 	var markupStr = $('#summernote').summernote('code');
	// 	$('.contents_tag').text(markupStr);
	// });

	// function summernoteSendFile(file){
	// 	var ext = file.name.split('.').pop().toLowerCase();
	// 	var sizeKB = file.size / 1024;
	// 	var sizeMB = Math.ceil(sizeKB/1024);

	// 	if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
	// 		$invalid_ext = false;
	// 	}
	// 	else
	// 	{
	// 	   $invalid_ext = true;
	// 	}

	// 	if($invalid_ext == true)
	// 	{
	// 		if(sizeKB <= 5024)
	// 		{
	// 			data = new FormData();
	// 			data.append("file",file);

	// 			var urls = hostname + '/summernote/upload';

	// 			$.ajax({
	// 				data:data,
	// 				type:"POST",
	// 				url:urls,
	// 				cache: false,
	// 				contentType: false,
	// 				processData: false,
	// 				success: function(data){
	// 					var obj = jQuery.parseJSON(data);
	// 					var image_url = '/upload/summernote/' + obj.hash_filename;
	// 					console.log(image_url);
	// 					$('#summernote').summernote('insertImage', image_url);
	// 				}
	// 			});
	// 		}
	// 		else
	// 		{
	// 			$('.tag-content-editor-msg').html('檔案大小限制為5MB, 您的檔案目前大小為'+ sizeMB +'MB已超過檔案大小限制!');
	// 			$('.tag-content-editor-msg').addClass('alert-warning');
	// 			$('.tag-content-editor-msg').show('blind').delay(3000).hide('blind');
	// 		}
	// 	}
	// 	else
	// 	{
	// 		$('.tag-content-editor-msg').html('檔案格式僅接受jpg, jpeg ,png, gif格式, 您的檔案格式為'+ ext +',不符合接受檔案格式!');
	// 		$('.tag-content-editor-msg').addClass('alert-warning');
	// 		$('.tag-content-editor-msg').show('blind').delay(5000).hide('blind');			
	// 	}
	// }

	// $('#summernote').on('summernote.image.upload', function(we, files) {
	// 	console.log(files[0]);
	// 	summernoteSendFile(files[0]);
	// });



	$('input[name="tag-content-onoffswitch"]').change(function(e){
		if($(this).is(":checked"))
		{
			// $('#summernote').summernote('enable');
			$('.contents_link').prop('disabled', false);
			
		}
		else
		{
			// $('#summernote').summernote('code','');
			// $('#summernote').summernote('disable');
			$('.contents_link').prop('disabled', true);

		}
	});

	$('.btnExecuteSaveContentTag').click(function(e){
		e.preventDefault();
		var content_uri = $('.contents_link').val();
		if(!content_uri.match(/https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,4}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g))
		{
			if($('.onoffswitch-checkbox').is(":checked"))
			{
				$(".tag-content-editor-msg").addClass('alert-warning');
				$(".tag-content-editor-msg").html('<i class="fa fa-times" style="color:red"></i> 連接格式錯誤！請重新輸入。');
				$(".tag-content-editor-msg").show('blind').delay(3000).hide('blind');
			}
			else{$('.formTagEdit').submit();}
		}else
		{
			$('.formTagEdit').submit();
		}
	});

	
});