<?php    
//## NextScripts App.net Connection Class
$nxs_snapAvNts[] = array('code'=>'XI', 'lcode'=>'xi', 'name'=>'XING');

if (!class_exists("nxs_snapClassXI")) { class nxs_snapClassXI { var $ntInfo = array('code'=>'XI', 'lcode'=>'xi', 'name'=>'XING', 'defNName'=>'', 'tstReq' => true);
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){  global $nxs_plurl, $nxs_snapSetPgURL,  $nxs_gOptions;  $ntInfo = $this->ntInfo;
    if ( isset($_GET['auth']) && $_GET['auth']==$ntInfo['lcode']){ require_once('apis/scOAuth.php'); $options = $ntOpts[$_GET['acc']];
              
              $consumer_key = $options['appKey']; $consumer_secret = $options['appSec'];
              $callback_url = $nxs_snapSetPgURL."&auth=".$ntInfo['lcode']."a&acc=".$_GET['acc'];
             
              $tum_oauth = new wpScoopITOAuth($consumer_key, $consumer_secret); 
              $tum_oauth->baseURL = 'https://api.xing.com'; $tum_oauth->request_token_path = '/v1/request_token';
              
              $request_token = $tum_oauth->getReqToken($callback_url); 
              $options['oAuthToken'] = $request_token['oauth_token'];
              $options['oAuthTokenSecret'] = $request_token['oauth_token_secret'];

              prr($tum_oauth); prr($options); //die();
              
              switch ($tum_oauth->http_code) { case 201: case 200: $url = 'https://api.xing.com/v1/authorize?oauth_token='.$options['oAuthToken'];                 
                if (function_exists('get_option')) $nxs_gOptions = get_option('NS_SNAutoPoster'); if(!empty($nxs_gOptions)){$nxs_gOptions[$ntInfo['lcode']][$_GET['acc']] = $options; nxs_settings_save($nxs_gOptions);}
                echo '<br/><br/>All good?! Redirecting ..... <script type="text/javascript">window.location = "'.$url.'"</script>'; break; 
                default: echo '<br/><b style="color:red">Could not connect to XING. Refresh the page or try again later.</b>'; die();
              }
              die();
            }
    if ( isset($_GET['auth']) && $_GET['auth']==$ntInfo['lcode'].'a'){ require_once('apis/scOAuth.php'); $options = $ntOpts[$_GET['acc']];
              $consumer_key = $options['appKey']; $consumer_secret = $options['appSec'];
            
              $tum_oauth = new wpScoopITOAuth($consumer_key, $consumer_secret, $options['oAuthToken'], $options['oAuthTokenSecret']); //prr($tum_oauth);
              $tum_oauth->baseURL = 'https://api.xing.com'; $tum_oauth->access_token_path = '/v1/access_token';
              
              $access_token = $tum_oauth->getAccToken($_GET['oauth_verifier']); prr($access_token);
              $options['accessToken'] = $access_token['oauth_token'];  $options['accessTokenSec'] = $access_token['oauth_token_secret'];              
              $optionsG = get_option('NS_SNAutoPoster'); $optionsG[$ntInfo['lcode']][$_GET['acc']] = $options;  update_option('NS_SNAutoPoster', $optionsG);              
              
              $tum_oauth = new wpScoopITOAuth($consumer_key, $consumer_secret, $options['accessToken'], $options['accessTokenSec']);               
              $uinfo = $tum_oauth->makeReq('https://api.xing.com/v1/users/me', ''); prr($uinfo);
              if (is_array($uinfo) && isset($uinfo['users']) && isset($uinfo['users'][0]) && is_array($uinfo['users'][0])) { $uinfo = $uinfo['users'][0]; $options['appPGUserName'] = $uinfo['page_name'];
                $options['appAppUserName'] = $uinfo['display_name']."(".$uinfo['page_name'].")"; $options['appAppUserID'] = $uinfo['id']; 
                if (function_exists('get_option')) $nxs_gOptions = get_option('NS_SNAutoPoster'); if(!empty($nxs_gOptions)){$nxs_gOptions[$ntInfo['lcode']][$_GET['acc']] = $options; nxs_settings_save($nxs_gOptions);}               
              } //prr($options); die();
              if (!empty($options['appAppUserID'])) {  echo '<br/><br/>All good?! Redirecting ..... <script type="text/javascript">window.location = "'.$nxs_snapSetPgURL.'"</script>'; die();}
                else die("<span style='color:red;'>ERROR: Authorization Error: <span style='color:darkred; font-weight: bold;'>".print_r($uinfo, true)."</span></span>");              
            }
    
  ?>    
    <div class="nxs_box">
      <div class="nxs_box_header"> 
        <div class="nsx_iconedTitle" style="margin-bottom:1px;background-image:url(<?php echo $nxs_plurl;?>img/<?php echo $ntInfo['lcode']; ?>16.png);"><?php echo $ntInfo['name']; ?>
          <?php $cbo = count($ntOpts); ?> 
          <?php if ($cbo>1){ ?><div class="nsBigText"><?php echo "(".($cbo=='0'?'No':$cbo)." "; _e('accounts', 'social-networks-auto-poster-facebook-twitter-g'); echo ")"; ?></div><?php } ?>
        </div>
      </div>
      <div class="nxs_box_inside">
        <?php foreach ($ntOpts as $indx=>$pbo){ if (trim($pbo['nName']=='')) $pbo['nName'] = $ntInfo['name']; 
          if (!isset($pbo[$ntInfo['lcode'].'OK']) || $pbo[$ntInfo['lcode'].'OK']=='') $pbo[$ntInfo['lcode'].'OK'] = (isset($pbo['appAppUserID']) && $pbo['appAppUserID']>1)?'1':'';
        ?>
            <p style="margin:0px;margin-left:5px;"> <img id="<?php echo $ntInfo['code'].$indx;?>LoadingImg" style="display: none;" src='<?php echo $nxs_plurl; ?>img/ajax-loader-sm.gif' />
            
            <input value="0" name="<?php echo $ntInfo['lcode']; ?>[<?php echo $indx; ?>][apDo<?php echo $ntInfo['code']; ?>]" type="hidden" />             
            <?php if ((int)$pbo['do'.$ntInfo['code']] == 1 && isset($pbo['catSel']) && (int)$pbo['catSel'] == 1) { ?> <input type="radio" name="<?php echo $ntInfo['lcode']; ?>[<?php echo $indx; ?>][apDo<?php echo $ntInfo['code']; ?>]" id="rbtn<?php echo $ntInfo['lcode'].$indx; ?>" value="1" checked="checked" onmouseout="nxs_hidePopUpInfo('popOnlyCat');" onmouseover="nxs_showPopUpInfo('popOnlyCat', event);" /> <?php } else { ?>            
            <input value="1" name="<?php echo $ntInfo['lcode']; ?>[<?php echo $indx; ?>][apDo<?php echo $ntInfo['code']; ?>]" type="checkbox" <?php if ((int)$pbo['do'.$ntInfo['code']] == 1 && $pbo['catSel']!='1') echo "checked"; ?> /> 
            
            <?php } ?>
            
            <?php if (isset($pbo['catSel']) && (int)$pbo['catSel'] == 1) { ?> <span onmouseout="nxs_hidePopUpInfo('popOnlyCat');" onmouseover="nxs_showPopUpInfo('popOnlyCat', event);"><?php echo "*[".(substr_count($pbo['catSelEd'], ",")+1)."]*" ?></span><?php } ?>
            <?php if (isset($pbo['rpstOn']) && (int)$pbo['rpstOn'] == 1) { ?> <span onmouseout="nxs_hidePopUpInfo('popReActive');" onmouseover="nxs_showPopUpInfo('popReActive', event);"><?php echo "*[R]*" ?></span><?php } ?>
            <strong><?php  _e('Auto-publish to', 'social-networks-auto-poster-facebook-twitter-g'); ?> <?php echo $ntInfo['name']; ?> <i style="color: #005800;"><?php if($pbo['nName']!='') echo "(".$pbo['nName'].")"; ?></i></strong>
          &nbsp;&nbsp;<?php if ($ntInfo['tstReq'] && (!isset($pbo[$ntInfo['lcode'].'OK']) || $pbo[$ntInfo['lcode'].'OK']=='')){ ?><b style="color: #800000"><?php  _e('Attention requred. Unfinished setup', 'social-networks-auto-poster-facebook-twitter-g'); ?> ==&gt;</b><?php } ?>
          <a id="do<?php echo $ntInfo['code'].$indx; ?>AG" href="#" onclick="doGetHideNTBlock('<?php echo $ntInfo['code'];?>' , '<?php echo $indx; ?>');return false;">[<?php  _e('Show Settings', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>&nbsp;&nbsp;          
          <a href="#" onclick="doDelAcct('<?php echo $ntInfo['lcode']; ?>', '<?php echo $indx; ?>', '<?php if (isset($pbo['bgBlogID'])) echo $pbo['nName']; ?>');return false;">[<?php  _e('Remove Account', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>
          </p><div id="nxsNTSetDiv<?php echo $ntInfo['code'].$indx; ?>"></div><?php //$pbo['ntInfo'] = $ntInfo; $this->showNTSettings($indx, $pbo);             
        }?>
      </div>
    </div> <?php 
  }  
  //#### Show NEW Settings Page
  function showNewNTSettings($options){ $opts = array('nName'=>'', 'doXI'=>'1', 'appKey'=>'', 'appSec'=>'', 'topicURL'=>'', 'inclTags'=>'1', 'postType'=>'A'); $opts['ntInfo']= $this->ntInfo; $this->showNTSettings($options, $opts, true);}
  //#### Show Unit  Settings
  function showNTSettings($ii, $options, $isNew=false){  global $nxs_plurl, $nxs_snapSetPgURL; $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; $ntU = $ntInfo['code']; 
    if (!isset($options['nHrs'])) $options['nHrs'] = 0; if (!isset($options['nMin'])) $options['nMin'] = 0;  if (!isset($options['catSel'])) $options['catSel'] = 0;  if (!isset($options['catSelEd'])) $options['catSelEd'] = ''; 
    if (!isset($options['nDays'])) $options['nDays'] = 0; if (!isset($options['qTLng'])) $options['qTLng'] = ''; if (!isset($options['topicURL'])) $options['topicURL'] = '';     
    if (!isset($options['appKey'])) $options['appKey'] = ''; if (!isset($options['appSec'])) $options['appSec'] = '';  ?>    
            <div id="do<?php echo $ntU; ?><?php echo $ii; ?>Div" class="insOneDiv<?php if ($isNew) echo " clNewNTSets"; ?>">     
            <input type="hidden" value="0" id="apDoS<?php echo $ntU.$ii; ?>" />
            <div class="nsx_iconedTitle" style="float: right; background-image: url(<?php echo $nxs_plurl; ?>img/<?php echo $nt; ?>16.png);"><a style="font-size: 12px;" target="_blank"  href="http://www.nextscripts.com/instructions/setup-installation-xing-social-networks-auto-poster/"><?php $nType=$ntInfo['name']; printf( __( 'Detailed %s Installation/Configuration Instructions', 'social-networks-auto-poster-facebook-twitter-g' ), $nType); ?></a></div>
            
            <div style="width:100%;"><strong><?php _e('Account Nickname', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong> <i><?php _e('Just so you can easily identify it', 'social-networks-auto-poster-facebook-twitter-g'); ?></i> </div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][nName]" id="apnName<?php echo $ii; ?>" style="font-weight: bold; color: #005800; border: 1px solid #ACACAC; width: 40%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['nName'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/>
            <?php echo nxs_addQTranslSel($nt, $ii, $options['qTLng']); ?>
            <br/>
                <ul class="nsx_tabs">
    <li><a href="#nsx<?php echo $nt.$ii ?>_tab1"><?php _e('Account Info', 'social-networks-auto-poster-facebook-twitter-g'); ?></a></li>    
    <?php if (!$isNew) { ?>  <li><a href="#nsx<?php echo $nt.$ii ?>_tab2"><?php _e('Advanced', 'social-networks-auto-poster-facebook-twitter-g'); ?></a></li>  <?php } ?>
    </ul>
    <div class="nsx_tab_container"><?php /* ######################## Account Tab ####################### */ ?>
    <div id="nsx<?php echo $nt.$ii ?>_tab1" class="nsx_tab_content" style="background-image: url(<?php echo $nxs_plurl; ?>img/<?php echo $nt; ?>-bg.png); background-repeat: no-repeat;  background-position:90% 10%;">
            
            <div style="width:100%;"><strong><?php echo $nType; ?> Consumer Key:</strong> </div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][appKey]" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['appKey'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" />                
            <div style="width:100%;"><strong><?php echo $nType; ?> Consumer Secret:</strong> </div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][appSec]"  style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['appSec'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" />  <br/>                
            
              <br/>  
                                    
            <div id="altFormat" style="margin-left: 0px;">
              <div style="width:100%;"><strong id="altFormatText"><?php _e('Message Text Format', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong> (<a href="#" id="msgFrmt<?php echo $ntU; ?><?php echo $ii; ?>HintInfo" onclick="mxs_showHideFrmtInfo('msgFrmt<?php echo $ntU; ?><?php echo $ii; ?>'); return false;"><?php _e('Show format info', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>)
              </div>
              
              <textarea cols="150" rows="3" id="<?php echo $nt; ?><?php echo $ii; ?>msgFrmt" name="<?php echo $nt; ?>[<?php echo $ii; ?>][msgFrmt]" style="width:51%;max-width: 650px;" onfocus="jQuery('#<?php echo $nt; ?><?php echo $ii; ?>msgFrmt').attr('rows', 6); mxs_showFrmtInfo('msgFrmt<?php echo $ntU.$ii; ?>');"><?php if ($isNew) _e("%EXCERPT% \r\n\r\n%URL%", 'social-networks-auto-poster-facebook-twitter-g'); else _e(apply_filters('format_to_edit', htmlentities($options['msgFrmt'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g'); ?></textarea><?php nxs_doShowHint("msgFrmt".$ntU.$ii); ?>
            </div>
   
            <p style="margin-bottom: 20px;margin-top: 5px;"><input value="1" type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][inclTags]"  <?php if ((int)$options['inclTags'] == 1) echo "checked"; ?> /> 
              <strong><?php _e('Post with tags', 'social-networks-auto-poster-facebook-twitter-g'); ?></strong>  <?php _e('Tags from the blogpost will be auto-posted to '.$ntInfo['name'], 'social-networks-auto-poster-facebook-twitter-g'); ?>                                                               
            </p>
            
            <div style="width:100%;"><strong id="altFormatText">Post Type:</strong></div>                      
            <div style="margin-left: 10px;">
              <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="T" <?php if ($options['postType'] == 'T') echo 'checked="checked"'; ?> /> <?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('just text message', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>                                  
              <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="A" <?php if ( !isset($options['postType']) || $options['postType'] == '' || $options['postType'] == 'A') echo 'checked="checked"'; ?> /> <?php _e('Post link to the blogpost', 'social-networks-auto-poster-facebook-twitter-g'); ?><br/>
            </div>
   
            <br/><br/>
            <?php  if($options['appKey']=='') { ?>
            <b><?php _e('Authorize Your '.$ntInfo['name'].' Account', 'social-networks-auto-poster-facebook-twitter-g'); ?></b> <?php _e('Please click "Update Settings" to be able to Authorize your account.', 'social-networks-auto-poster-facebook-twitter-g'); ?>
            <?php } else { if(isset($options['appAppUserID']) && $options['appAppUserID']>0) { ?>
            <?php _e('Your '.$ntInfo['name'].' Account has been authorized.', 'social-networks-auto-poster-facebook-twitter-g'); ?> User ID: <?php _e(apply_filters('format_to_edit', htmlentities($options['appAppUserID'].' - '.$options['appAppUserName'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>.
            <?php _e('You can', 'social-networks-auto-poster-facebook-twitter-g'); ?> Re- <?php } ?>            
            <a href="<?php echo $nxs_snapSetPgURL;?>&auth=<?php echo $nt; ?>&acc=<?php echo $ii; ?>">Authorize Your <?php echo $ntInfo['name']; ?> Account</a> 
            
            <?php if (!isset($options['appAppUserID']) || $options['appAppUserID']<1) { ?> <div class="blnkg">&lt;=== <?php _e('Authorize your account', 'social-networks-auto-poster-facebook-twitter-g'); ?> ===</div> <?php }?>
            <?php } ?>
            <br/><br/>            
            
            <?php if ($isNew) { ?> <input type="hidden" name="<?php echo $nt; ?>[<?php echo $ii; ?>][apDo<?php echo $ntU; ?>]" value="1" id="apDoNew<?php echo $ntU; ?><?php echo $ii; ?>" /> <?php } ?>
            <?php if (isset($options['appAppUserID']) && $options['appAppUserID']>0) { ?>
            
            <b><?php _e('Test your settings', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</b>&nbsp;&nbsp;&nbsp; <a href="#" class="NXSButton" onclick="testPost('<?php echo $ntU; ?>', '<?php echo $ii; ?>'); return false;"><?php printf( __( 'Submit Test Post to %s', 'social-networks-auto-poster-facebook-twitter-g' ), $nType); ?></a>              <?php } 
            ?>
    
     </div>
      <?php /* ######################## Tools Tab ####################### */ ?>
    <?php if (!$isNew) { ?><div id="nsx<?php echo $nt.$ii ?>_tab2" class="nsx_tab_content">    
    <?php nxs_showCatTagsCTFilters($nt, $ii, $options);
          nxs_addPostingDelaySelV3($nt, $ii, $options['nHrs'], $options['nMin'], $options['nDays']);
          nxs_showRepostSettings($nt, $ii, $options); ?>      
    </div> <?php } ?>       <?php /* #### End of Tab #### */ ?>
    </div><br/> <?php /* #### End of Tabs #### */ ?>
    
    <div class="submitX nxclear" style="padding-bottom: 0px;"><input type="submit" class="button-primary" name="update_NS_SNAutoPoster_settings" value="<?php _e('Update Settings', 'social-networks-auto-poster-facebook-twitter-g') ?>" /></div>
            
            
            </div><?php
  }
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ $code = $this->ntInfo['code']; 
    foreach ($post as $ii => $pval){ 
      if (isset($pval['appKey']) && $pval['appKey']!=''){ if (!isset($options[$ii])) $options[$ii] = array();
        
        if (isset($pval['apDo'.$code])) $options[$ii]['do'.$code] = $pval['apDo'.$code]; else $options[$ii]['do'.$code] = 0; 
        if (isset($pval['nName']))  $options[$ii]['nName'] = trim($pval['nName']);  
        
        if (isset($pval['appKey'])) $options[$ii]['appKey'] = trim($pval['appKey']);        
        if (isset($pval['appSec'])) $options[$ii]['appSec'] = trim($pval['appSec']);        
        if (isset($pval['topicURL'])) $options[$ii]['topicURL'] = trim($pval['topicURL']);                  
        
                
        if (isset($pval['catSel'])) $options[$ii]['catSel'] = trim($pval['catSel']); else $options[$ii]['catSel'] = 0;
        if ($options[$ii]['catSel']=='1' && trim($pval['catSelEd'])!='') $options[$ii]['catSelEd'] = trim($pval['catSelEd']); else $options[$ii]['catSelEd'] = '';                                 
        
        if (isset($pval['postType'])) $options[$ii]['postType'] = $pval['postType'];         
        if (isset($pval['inclTags'])) $options[$ii]['inclTags'] = trim($pval['inclTags']); else $options[$ii]['inclTags'] = 0;
        if (isset($pval['msgFrmt'])) $options[$ii]['msgFrmt'] = trim($pval['msgFrmt']);        
        
        $options[$ii] = nxs_adjRpst($options[$ii], $pval);       
        
        if (isset($pval['delayDays'])) $options[$ii]['nDays'] = trim($pval['delayDays']);
        if (isset($pval['delayHrs'])) $options[$ii]['nHrs'] = trim($pval['delayHrs']); if (isset($pval['delayMin'])) $options[$ii]['nMin'] = trim($pval['delayMin']); 
        if (isset($pval['qTLng'])) $options[$ii]['qTLng'] = trim($pval['qTLng']); 
      } elseif ( count($pval)==1 ) if (isset($pval['apDo'.$code])) $options[$ii]['do'.$code] = $pval['apDo'.$code]; else $options[$ii]['do'.$code] = 0; 
    } return $options;
  }  
  //#### Show Post->Edit Meta Box Settings
  function showEdPostNTSettings($ntOpts, $post){ global $nxs_plurl; $post_id = $post->ID; $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code'];
     foreach($ntOpts as $ii=>$ntOpt)  { $pMeta = maybe_unserialize(get_post_meta($post_id, 'snap'.$ntU, true));  
        if (is_array($pMeta) && isset($pMeta[$ii]) && is_array($pMeta[$ii])) $ntOpt = $this->adjMetaOpt($ntOpt, $pMeta[$ii]);  
        if (empty($ntOpt['imgToUse'])) $ntOpt['imgToUse'] = ''; if (empty($ntOpt['urlToUse'])) $ntOpt['urlToUse'] = '';
        $doIT = $ntOpt['do'.$ntU] && (is_array($pMeta) || $ntOpt['catSel']!='1'); $imgToUse = $ntOpt['imgToUse']; $urlToUse = $ntOpt['urlToUse']; 
        $isAvail = $ntOpt['appKey']!='' && $ntOpt['appSec']!=''; $msgFormat = htmlentities($ntOpt['msgFrmt'], ENT_COMPAT, "UTF-8");  
        $postType = $ntOpt['postType'];         
      ?>  
      <tr><th style="text-align:left;" colspan="2">
      <?php if ($ntOpt['catSel']=='1' && trim($ntOpt['catSelEd'])!='')  { ?> <input type="hidden" class="nxs_SC" id="nxs_SC_<?php echo $ntU; ?><?php echo $ii; ?>" value="<?php echo $ntOpt['catSelEd']; ?>" /> <?php } ?>
      <?php if (!empty($ntOpt['tagsSelX'])) { ?>  <input type="hidden" class="nxs_TG" id="nxs_TG_<?php echo $ntU; ?><?php echo $ii; ?>" value="<?php echo $ntOpt['tagsSelX']; ?>" /> <?php } ?>
      <?php if ($isAvail) { ?><input class="nxsGrpDoChb" value="1" id="do<?php echo $ntU.$ii; ?>" <?php if ($post->post_status == "publish") echo 'disabled="disabled"';?> type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][do<?php echo $ntU; ?>]" <?php if ((int)$doIT == 1) echo 'checked="checked" title="def"';  ?> /> 
      <?php if ($post->post_status == "publish") { ?> <input type="hidden" name="<?php echo $nt; ?>[<?php echo $ii; ?>][do<?php echo $ntU; ?>]" value="<?php echo $doIT;?>"> <?php } ?> <?php } ?>
      
      <div class="nsx_iconedTitle" style="display: inline; font-size: 13px; background-image: url(<?php echo $nxs_plurl; ?>img/<?php echo $nt; ?>16.png);"><?php echo $this->ntInfo['name']; ?> - <?php _e('publish to', 'social-networks-auto-poster-facebook-twitter-g') ?> (<i style="color: #005800;"><?php echo $ntOpt['nName']; ?></i>)</div></th> <td><?php //## Only show RePost button if the post is "published"
                    if ($post->post_status == "publish" && $isAvail) { ?><input alt="<?php echo $ii; ?>" style="float: right;" onmouseout="hidePopShAtt('SV');" onmouseover="showPopShAtt('SV', event);" onclick="return false;" type="button" class="button" name="rePostTo<?php echo $ntU; ?>_repostButton" id="rePostTo<?php echo $ntU; ?>_button" value="<?php _e('Repost to '.$this->ntInfo['name'], 'social-networks-auto-poster-facebook-twitter-g') ?>" />
                    <?php  } ?>
                    
                    <?php  if (is_array($pMeta) && is_array($pMeta[$ii]) && isset($pMeta[$ii]['postURL']) ) { 
                        
                        ?> <span id="pstd<?php echo $ntU; ?><?php echo $ii; ?>" style="float: right;padding-top: 4px; padding-right: 10px;">
                      <a style="font-size: 10px;" href="<?php echo $pMeta[$ii]['postURL']; ?>" target="_blank"><?php $nType=$this->ntInfo['name']; printf( __( 'Posted on', 'social-networks-auto-poster-facebook-twitter-g' ), $nType); ?>  <?php echo (isset($pMeta[$ii]['pDate']) && $pMeta[$ii]['pDate']!='')?(" (".$pMeta[$ii]['pDate'].")"):""; ?></a>
                    </span><?php } ?>
                    
                </td></tr>                
                
                <?php if (!$isAvail) { ?><tr><th scope="row" style="text-align:right; width:150px; padding-top: 5px; padding-right:10px;"></th> <td><b>Setup your <?php echo $this->ntInfo['name']; ?> Account to AutoPost to <?php echo $this->ntInfo['name']; ?></b></td></tr>
                <?php }  else { if ($post->post_status != "publish" && function_exists('nxs_doSMAS5') ) { $ntOpt['postTime'] = get_post_time('U', false, $post_id); nxs_doSMAS5($nt, $ii, $ntOpt); } ?>
                
                <?php if ($ntOpt['rpstOn']=='1') { ?> 
                
                <tr id="altFormat1" style=""><th scope="row" class="nxsTHRow">
                <input value="0"  type="hidden" name="<?php echo $nt; ?>[<?php echo $ii; ?>][rpstPostIncl]"/><input value="nxsi<?php echo $ii; ?>ap" type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][rpstPostIncl]"  <?php if (!empty($ntOpt['rpstPostIncl'])) echo "checked"; ?> /> 
                </th>
                <td> <?php _e('Include in "Auto-Reposting" to this network.', 'social-networks-auto-poster-facebook-twitter-g') ?>
                </td></tr> <?php } ?>
     
                <tr id="altFormat1" style=""><th scope="row" style="vertical-align:top;  padding-top: 6px; text-align:right; width:60px; padding-right:10px;"><?php _e('Text Format:', 'social-networks-auto-poster-facebook-twitter-g') ?></th><td>                
                
                <textarea cols="150" rows="1" id="<?php echo $nt.$ii; ?>msgFrmt" name="<?php echo $nt; ?>[<?php echo $ii; ?>][msgFrmt]"  style="width:60%;max-width: 610px;" onfocus="jQuery('#<?php echo $nt.$ii; ?>msgFrmt').attr('rows', 4); jQuery('.nxs_FRMTHint').hide();mxs_showFrmtInfo('msgFrmt<?php echo $nt.$ii; ?>');"><?php echo $msgFormat ?></textarea> <?php nxs_doShowHint("msgFrmt".$nt.$ii, '', '58'); ?>
                
                </td></tr>
                
                <tr><th scope="row" style="text-align:right; width:150px; vertical-align:top; padding-top: 0px; padding-right:10px;"> <?php _e('Post Type:', 'social-networks-auto-poster-facebook-twitter-g') ?> <br/></th><td>     
        <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="T" <?php if ($postType == 'T') echo 'checked="checked"'; ?> /> <?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g') ?>  - <i><?php _e('just text message', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/>
        <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="A" <?php if ( !isset($postType) || $postType == '' || $postType == 'A') echo 'checked="checked"'; ?> /><?php _e('Text Post with "attached" blogpost', 'social-networks-auto-poster-facebook-twitter-g') ?>
     </td></tr>
                
                 <?php /* ## Select Image & URL ## */  nxs_showURLToUseDlg($nt, $ii, $urlToUse);  ?>
       <?php } 

     }
  }
  //#### Save Meta Tags to the Post
  function adjMetaOpt($optMt, $pMeta){ if (isset($pMeta['isPosted'])) $optMt['isPosted'] = $pMeta['isPosted']; else  $optMt['isPosted'] = ''; 
    if (isset($pMeta['doXI'])) $optMt['doXI'] = $pMeta['doXI'] == 1?1:0; else { if (isset($pMeta['msgFormat'])) $optMt['doXI'] = 0; } 
    
    if (isset($pMeta['msgFrmt'])) $optMt['msgFrmt'] = $pMeta['msgFrmt']; 
    if (isset($pMeta['postType'])) $optMt['postType'] = $pMeta['postType']; 
    
    if (isset($pMeta['imgToUse'])) $optMt['imgToUse'] = $pMeta['imgToUse'];  if (isset($pMeta['urlToUse'])) $optMt['urlToUse'] = $pMeta['urlToUse'];  
    if (isset($pMeta['timeToRun']))  $optMt['timeToRun'] = $pMeta['timeToRun'];  if (isset($pMeta['rpstPostIncl']))  $optMt['rpstPostIncl'] = $pMeta['rpstPostIncl'];    
    if (isset($pMeta['SNAPincludeXI']) && $pMeta['SNAPincludeXI'] == '1' ) $optMt['doXI'] = 1;  
    return $optMt;
  }  
}}
if (!function_exists("nxs_rePostToXI_ajax")) {
  function nxs_rePostToXI_ajax() { check_ajax_referer('nxsSsPageWPN');  $postID = $_POST['id']; global $plgn_NS_SNAutoPoster;  if (!isset($plgn_NS_SNAutoPoster)) return; $options = $plgn_NS_SNAutoPoster->nxs_options; 
    foreach ($options['xi'] as $ii=>$two) if ($ii==$_POST['nid']) {   $two['ii'] = $ii; $two['pType'] = 'aj'; //if ($two['apPageID'].$two['apUName']==$_POST['nid']) {  
      $appo =  get_post_meta($postID, 'snapXI', true); $appo =  maybe_unserialize($appo);// prr($appo);
      if (is_array($appo) && isset($appo[$ii]) && is_array($appo[$ii])){ $ntClInst = new nxs_snapClassXI(); $two = $ntClInst->adjMetaOpt($two, $appo[$ii]); } 
      $result = nxs_doPublishToXI($postID, $two); if ($result == 200) die("Successfully sent your post to XING. "); else die($result);        
    }    
  }
}  
if (!function_exists("nxs_doPublishToXI")) { //## Post to XI. // V3 - imgToUse - Done, class_SNAP_AP - Done, New Format - Done
  function nxs_doPublishToXI($postID, $options){ global $plgn_NS_SNAutoPoster; $ntCd = 'XI'; $ntCdL = 'xi'; $ntNm = 'XING'; if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true));
      $addParams = nxs_makeURLParams(array('NTNAME'=>$ntNm, 'NTCODE'=>$ntCd, 'POSTID'=>$postID, 'ACCNAME'=>$options['nName']));   
      if (empty($options['imgToUse'])) $options['imgToUse'] = ''; if (empty($options['imgSize'])) $options['imgSize'] = '';
      $ii = $options['ii']; if (!isset($options['pType'])) $options['pType'] = 'im'; if ($options['pType']=='sh') sleep(rand(1, 10)); 
      $logNT = '<span style="color:#800000">XING</span> - '.$options['nName'];      
      $snap_ap = get_post_meta($postID, 'snap'.$ntCd, true); $snap_ap = maybe_unserialize($snap_ap);     
      if ($options['pType']!='aj' && is_array($snap_ap) && (nxs_chArrVar($snap_ap[$ii], 'isPosted', '1') || nxs_chArrVar($snap_ap[$ii], 'isPrePosted', '1'))) {
        $snap_isAutoPosted = get_post_meta($postID, 'snap_isAutoPosted', true); if ($snap_isAutoPosted!='2') {  
           nxs_addToLogN('W', 'Notice', $logNT, '-=Duplicate=- Post ID:'.$postID, 'Already posted. No reason for posting duplicate'.' |'); return;
        }
      } $message = array('message'=>'', 'link'=>'', 'imageURL'=>'', 'videoURL'=>''); 
      
      if ($postID=='0') { echo "Testing ... <br/><br/>"; $message['description'] = 'Test Post, Description';  $message['title'] = 'Test Post - Title';  $message['url'] = home_url(); } 
      else { nxs_metaMarkAsPosted($postID, $ntCd, $options['ii'], array('isPrePosted'=>'1'));  $post = get_post($postID); if(!$post) return; 
        $postType = $options['postType']; $isNoImg = false; $tags = '';
        
        $options['msgFrmt'] = nsFormatMessage($options['msgFrmt'], $postID, $addParams); 
        
        $tggs = array(); if ($options['inclTags']=='1'){ $t = wp_get_post_tags($postID); $tggs = array(); foreach ($t as $tagA) {$tggs[] = $tagA->name;} $tags = $tggs; }        
                
        if ($postType=='A') if (trim($options['imgToUse'])!='') $imgURL = $options['imgToUse']; else $imgURL = nxs_getPostImage($postID, 'medium');  
        if ($postType=='I') if (trim($options['imgToUse'])!='') $imgURL = $options['imgToUse']; else $imgURL = nxs_getPostImage($postID, 'full');      
        if (preg_match("/noImg.\.png/i", $imgURL)) { $imgURL = ''; $isNoImg = true; }
        
        //## MyURL - URLToGo code
        $options = nxs_getURL($options, $postID, $addParams); $urlToGo = $options['urlToUse'];  $message = array('url'=>$urlToGo, 'imageURL'=>$imgURL, 'noImg'=>$isNoImg, 'tags'=>$tags);                 
        $extInfo = ' | PostID: '.$postID." - ".(isset($post) && is_object($post)?$post->post_title:''); 
      }            
      //## Actual Post
      $ntToPost = new nxs_class_SNAP_XI(); $ret = $ntToPost->doPostToNT($options, $message); //prr($ret);
      //## Process Results
      if (!is_array($ret) || $ret['isPosted']!='1') { //## Error 
         if ($postID=='0') prr($ret); nxs_addToLogN('E', 'Error', $logNT, '-=ERROR=- '.print_r($ret, true), $extInfo); 
      } else {  // ## All Good - log it.
        if ($postID=='0')  { nxs_addToLogN('S', 'Test', $logNT, 'OK - TEST Message Posted '); echo _e('OK - Message Posted, please see your '.$logNT.' Page. ', 'social-networks-auto-poster-facebook-twitter-g'); } 
          else  { nxs_metaMarkAsPosted($postID, $ntCd, $options['ii'], array('isPosted'=>'1', 'pgID'=>$ret['postID'], 'postURL'=>$ret['postURL'], 'pDate'=>date('Y-m-d H:i:s'))); 
           $extInfo .= ' | <a href="'.$ret['postURL'].'" target="_blank">Post Link</a>'; nxs_addToLogN('S', 'Posted', $logNT, 'OK - Message Posted ', $extInfo); }
      }
      //## Return Result
      if ($ret['isPosted']=='1') return 200; else return print_r($ret, true);      
      
  } 
}  
?>