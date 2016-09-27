/* <![CDATA[ */
	var smart = {
		settings : {
			pathSmart : './',
			pathImages : '../../images/',
			pathInstaller : 'smart_installer',
			pathResponser : 'smart_response',
			pathDoer : 'smart_doer',
			activeClass : 'smart_active',
			verifyFixWarn: '{fix:warning}',
			verifyFixMess: '{fix:message}',
			verifyDelMess: '{del:message}',
			sysVersion: '{sys:version}',
			verWarnMess: '{ver:warning}',
			statDeleting: '{act:deleting}',
			statInstalled: '{act:installed}',
			abortAction: false
		}
	};
	jQuery(document).ready(function() {
		if (!jQuery('#Errors').hasClass('smart_nohide')) {
			jQuery('#Errors').hide();
		}
	});
	function hideErrors() {
		$('#Errors').fadeTo('slow', 0.01, function(){
			$(this).slideUp('slow', function(){
				$(this).hide();
			});	
		});
	}
	function ResetWaiters() {
		$('img.smart_waiter').each(function(){
			$(this).fadeOut(400);
		});
		return true;
	}
	function RemoveClasses() {
		$('li.clickable').each(function(){
			if ($(this).hasClass(smart.settings.activeClass)) {
				$(this).removeClass(smart.settings.activeClass);
			}
		});
		return true;
	}
	function AbortActionSet() {
		smart.settings.abortAction = true;
		return true;
	}
	function VerifyFixForm(inputform) {
		if (!smart.settings.abortAction) {
			if ($(inputform.addon_id).val()==0) {
				alert(smart.settings.verifyFixWarn);
				return false;
			}
			return confirm(smart.settings.verifyFixMess);
		}
	}
	function CutWords(s, len) {
		n_str = s;
		if (n_str.length > len) {
			n_str = n_str.substr(0, (parseInt(len) - 3));
			n_str += '...';
		}
		return n_str;
	}
	function ChangeRank(rank) {
		cert = '';
		if (rank!=1 && rank!=2 && rank!=3) { rank = 0; }
		if (rank != 0) {
			cert = '<img src=\''+smart.settings.pathSmart+'graphics/certificate_rank_'+rank+'.png\' style=\'border:none;\' alt=\'\' />';
		}
		return cert;
	}
	function _DeleteAddon(addon_id) {
		$obj = $('#'+addon_id);
		if (confirm(smart.settings.verifyDelMess.split('%s').join($obj.attr('title')))) {
			$div = $obj.children('div');
			dText = $div.text();
			$div.html('').append('<img src=\''+smart.settings.pathSmart+'graphics/horizontal.gif\' class=\'smart_valign_fix\' alt=\'\' />');
			$.ajax({
				type: 'GET',
				url: smart.settings.pathSmart+smart.settings.pathDoer+'.php?addon_id='+addon_id,
				success: function(response) {
					data = response.split('||');
					$('#Errors').html(data['1']).fadeTo(400, 1.00);
					window.setTimeout('hideErrors();', 5000);
					if (data['0']==0) {
						$div.remove();
						$obj.removeClass('unclickable').addClass('clickable');
						$('#Packages').text(parseInt($('#Packages').text()) - 1);
						origTitle = $obj.text();
						lastTitle = CutWords($obj.attr('title'), 25);
						context = $obj.html();
						context = context.split(origTitle).join(lastTitle);
						$obj.html(context);
					} else {
						$div.text(dText);
					}
				}
			});
		}
	}
	function _LearnAddon(addon_id) {
		if (ResetWaiters()) { $('#Waiter_'+addon_id).fadeIn(300); }
		if (RemoveClasses()) { $('#'+addon_id).addClass(smart.settings.activeClass); }
		$.ajax({
			type: 'GET',
			url: smart.settings.pathSmart+smart.settings.pathResponser+'.php?addon_id='+addon_id,
			success: function(response) {
				data = response.split('||');
				if (data['0']==0) {
					$('#Title').text(data['2']);
					$('#Info').html(data['3']);
					$('#Author').text(data['6']);
					$('#Pubdate').text(data['7']);
					$('#Compatible').text(data['8']);
					$('#Size').text(data['9']);
					$('#Type').text(data['10']);
					$('#Addon').text(data['12']);
					$('#Panorama').attr({'href':data['13']});
					$('#Certificate').html(ChangeRank(data['14']));
					if (data['11']!='') {
						$('#Preview').fadeOut(300, function(){
							$('#Loader').fadeIn('300');
							$('#Preview').attr('src', data['11']).load(function(){
								$('#Loader').fadeOut(300, function(){
									$('#Preview').fadeTo(400, 1.0);
								});
							});
						});
					}
					$('#Install').fadeIn(300);
					$('#Waiter_'+addon_id).fadeOut(300);
				} else {
					$('#Errors').html(data['1']).fadeTo(400, 1.00);
					window.setTimeout('hideErrors();', 5000);
				}
			}
		});
	}
	var sliding = true;
	$('ul.smart_list').children('li').click(function(){
		state = $(this).children('ul').css('display');
		if (state=='none') {
			$(this).children('ul').slideToggle(400, function(){
				$(this).children('ul').fadeTo(400, 1.0).css({'display':'block'});
			});
			$(this).children('img').attr('src', smart.settings.pathImages+'minus.gif');
		} else {
			if (sliding) {
				$(this).children('ul').slideToggle(400, function(){
					$(this).children('ul').fadeTo(400, 0.01).css({'display':'none'});
				});
				$(this).children('img').attr('src', smart.settings.pathImages+'plus.gif');
			} else {
				sliding = true;
			}
		}
	});
	$('#SlideUp').click(function(){
		$('ul.smart_list').children('li').children('ul').each(function(){
			$(this).slideUp(400);
			$(this).parent('li').children('img').attr('src', smart.settings.pathImages+'plus.gif');
		});
	});
	$('#SlideDown').click(function(){
		$('ul.smart_list').children('li').children('ul').each(function(){
			$(this).slideDown(400);
			$(this).parent('li').children('img').attr('src', smart.settings.pathImages+'minus.gif');
		});
	});
	$('#SlideToggle').click(function(){
		$('ul.smart_list').children('li').click();
	});
	$('li.clickable').click(function(){
		sliding = false;
		addon_id = $(this).attr('id');
		if ($('#'+addon_id).hasClass('clickable')) {
			_LearnAddon(addon_id)
		} else {
			_DeleteAddon(addon_id);
		}
	});
	$('li.unclickable').click(function(){
		sliding = false;
		addon_id = $(this).attr('id');
		if ($('#'+addon_id).hasClass('unclickable')) {
			_DeleteAddon(addon_id);
		} else {
			_LearnAddon(addon_id);
		}
	});
	$('#Install').click(function(){
		addon_id = $('#Addon').text();
		cont = true;
		compatible = $('#Compatible').text().split('x').join('');
		if (!(smart.settings.sysVersion.indexOf(compatible) + 1)) {
			cont = confirm(smart.settings.verWarnMess.split('%s').join($('#Compatible').text()).split('%n').join('\n\r'));
		}
		if (cont) {
			$('#InstallLoader').fadeIn(300);
			$.ajax({
				type: 'GET',
				url: smart.settings.pathSmart+smart.settings.pathInstaller+'.php?addon_id='+addon_id,
				success: function(response) {
					data = response.split('||');
					$('#Errors').html(data['1']).fadeTo(400, 1.00);
					window.setTimeout('hideErrors();', 5000);
					$('#InstallLoader').fadeOut(300);
					if (data['0']==0) {
						$('#Packages').text(parseInt($('#Packages').text()) + 1);
						$('#Install').hide(200);
						origTitle = $('#'+addon_id).text();
						lastTitle = CutWords(origTitle, 15);
						context = $('#'+addon_id).html();
						context = context.split(origTitle).join(lastTitle);
						$('#'+addon_id).removeClass(smart.settings.activeClass).removeClass('clickable').addClass('unclickable');
						$('#'+addon_id).html('<div class=\'smart_status\' title=\''+smart.settings.statDeleting+'\'>'+smart.settings.statInstalled+'</div>' + context);
					}
				}
			});
		}
	});
/* ]]>*/