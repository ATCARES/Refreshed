<?php
/**
 * @file
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die();
}

// inherit main code from SkinTemplate, set the CSS and template filter
class SkinRefreshed extends SkinTemplate {
	var $useHeadElement = true;

	function initPage( OutputPage $out ) {
		parent::initPage( $out );
		$this->skinname  = 'refreshed';
		$this->stylename = 'refreshed';
		$this->template  = 'RefreshedTemplate';

		$out->addModuleScripts( 'skins.refreshed' );

		$out->addMeta( 'viewport', 'width=device-width' );
	}

	function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );

		$out->addModuleStyles( 'skins.refreshed' );
	}
}

$refreshedTOC = '';

class RefreshedTemplate extends BaseTemplate {

	public static function onOutputPageParserOutput( OutputPage &$out, ParserOutput $parseroutput ) {
		global $refreshedTOC;
		$refreshedTOC = $parseroutput->mSections;

		return true;
	}

	public function execute() {
		global $refreshedTOC;

		// suppress warnings to prevent notices about missing indexes in $this->data
		wfSuppressWarnings();

		$this->html( 'headelement' );

		// new TOC processing
		$tocHTML = '';
		foreach ( $refreshedTOC as $tocpart ) {
			//var_dump( $tocpart );
			$class = "toclevel-{$tocpart['toclevel']}";
			$tocHTML .= "<a href='#{$tocpart['anchor']}' class='$class'>{$tocpart['line']}</a>";
		}

		// Title processing
		$myTitle = $this->data['titletext'];

		$mySideTitle = $this->data['title'];
		if ( $this->getSkin()->getTitle()->getNamespace() == 0 && substr_count( $mySideTitle, 'editing' ) == 0 ) {
			$mySideTitle = "Article:$mySideTitle";
		}
		$mySideTitle = str_replace( '/', '<wbr>/<wbr>', $mySideTitle );
		$mySideTitle = str_replace( ':', '<wbr>:<wbr>', $mySideTitle );
?>

	<div id="header">
		<?php
		$logos = array(
			'meta' => "<img src='$IP/skins/refreshed/project-images/brickimedia.png' />",
			'en' => "<img src='$IP/skins/refreshed/project-images/brickipedia.png' />",
			'customs' => "<img src='$IP/skins/refreshed/project-images/customs.png' />",
			'stories' => "<img src='$IP/skins/refreshed/project-images/stories.png' />",
			'cuusoo' => "<img src='$IP/skins/refreshed/project-images/cuusoo.png' />",
			'admin' => "<img height=22 src='$IP/skins/refreshed/project-images/admin.png' />",
			'dev' => "<img height=26 src='$IP/skins/refreshed/project-images/dev.png' />"
		);

		global $bmProject;
		?>
		<div id="siteinfo">
			<a href='javascript:;'>
				<?php echo $logos[$bmProject];
					unset( $logos[$bmProject] );
					echo "<img class='arrow' src='$IP/skins/refreshed/arrow.png'/>";
				?>
			</a>
			<ul class="headermenu" style="display:none;">
				<?php
					foreach ( $logos as $site => $logo ) {
						echo "<a href=\"http://$site.brickimedia.org\">{$logo}</a>";
					}
				?>
			</ul>
		</div>
	</div>
	<div id="fullwrapper">
		<div id="leftbar">
			<div class="shower">
				<?php echo "<img class='arrow' src='$IP/skins/refreshed/mobile-expand-edit.png'/>"; ?>
			</div>
			<div id="userinfo">
				<a href='javascript:;'>
					<?php global $wgUser, $wgArticlePath;
						$id = $wgUser->getId();
						if ( is_file( '/var/www/wiki/images/avatars/' . $id . '_m.png' ) ) {
							$avatar = '/images/avatars/' . $id . '_m.png';
						} elseif ( is_file( '/var/www/wiki/images/avatars/' . $id . '_m.jpg' ) ) {
							$avatar = '/images/avatars/' . $id . '_m.jpg';
						} elseif ( is_file( '/var/www/wiki/images/avatars/' . $id . '_m.gif' ) ) {
							$avatar = '/images/avatars/' . $id . '_m.gif';
						} else {
							$avatar = '/images/avatars/default_m.gif';
						}
						echo "<img class='arrow' src='$IP/skins/refreshed/arrow.png'/><img class='avatar' src='http://meta.brickimedia.org" . $avatar . "' /><span>$wgUser</span>";
					?>
				</a>
				<ul class="headermenu" style="display:none;">
					<?php
						foreach ( $this->getPersonalTools() as $key => $tool ) {
							foreach ( $tool['links'] as $linkKey => $link ) {
								echo $this->makeLink( $linkKey, $link, $options );
							}
						}
					?>
				</ul>
				<img class="avatar" />
			</div>
			<div id="leftbar-main">
				<div id="leftbar-top">
					<div id="pagelinks">
						<?php
						reset( $this->data['content_actions'] );
						$pageTab = key( $this->data['content_actions'] );

						$this->data['content_actions'][$pageTab]['text'] = $mySideTitle;

						foreach ( $this->data['content_actions'] as $action ) {
					 		echo '<a class="' . $action['class'] . '" ' .
					 			'id="' . $action['id'] . '" ' .
					 			'href="' . htmlspecialchars( $action['href'] ) . '">' .
					 			$action['text'] . '</a>'; // no htmlspecialchars
						} ?>
					</div>
				</div>
				<div id="leftbar-bottom">
					<div id="refreshed-toc">
						<div id="toc-box"></div>
						<!-- <div> -->
							<?php echo $tocHTML; ?>
						<!-- </div> -->
					</div>
				</div>
			</div>
		</div>
		<div id="contentwrapper">
        	<?php if ( $this->data['sitenotice'] ) { ?>
                <div id="site-notice">
                    <?php $this->html('sitenotice'); ?>
                </div>
            <?php } ?>
            <div id="newtalk"><?php $this->html( 'newtalk' )  ?></div>
			<div id="maintitle">
				<h1>
					<?php echo $myTitle; ?>
					<h1 class="title-overlay">&nbsp;</h1>
				</h1>

			</div>
			<div id="smalltoolboxwrapper">
				<div id="smalltoolbox">
					<?php
					reset( $this->data['content_actions'] );
					$pageTab = key( $this->data['content_actions'] );

					$this->data['content_actions'][$pageTab]['text'] = $mySideTitle;

					$firstAction = true;
					foreach ( $this->data['content_actions'] as $action ) {
						if ( !$firstAction ) {
							echo '<a href="' . htmlspecialchars( $action['href'] ) . '"><i class="icon-2x icon-link" id="icon-' . $action['id'] . '"></i></a>';
						} else {
							$firstAction = false;
						}
					} ?>
				</div>
				<a href="javascript:;"><i class="icon-ellipsis-horizontal icon-2x icon-link"></i></a>
			</div>
			<div id="content">
				<?php $this->html( 'bodytext' ); ?>
			</div>
			<div id="cats">
				<?php $this->html( 'catlinks' ); ?>
			</div>
			<br clear="all" />
		</div>
		<div id="rightbar">
			<div class="shower">
				<?php echo "<img class='arrow' src='$IP/skins/refreshed/mobile-expand.png'/>"; ?>
			</div>
			<div id="search">
				<form action="<?php $this->text( 'wgScript' ) ?>" method="get">
					<input type="text" name="search" placeholder="search" />
				</form>
			</div>
			<div id="rightbar-main">
				<div id="rightbar-top">
					<?php
						unset( $this->data['sidebar']['SEARCH'] );
						unset( $this->data['sidebar']['TOOLBOX'] );
						unset( $this->data['sidebar']['LANGUAGES'] );

						foreach ( $this->data['sidebar'] as $main => $sub ) {
							echo '<span class="main">' . htmlspecialchars( $main ) . '</span>';
							foreach ( $sub as $action ) {
					 			echo '<a class="sub" id="' . $action['id'] . '" ' .
					 				'href="' . htmlspecialchars( $action['href'] ) . '">' .
					 				htmlspecialchars( $action['text'] ) . '</a>';
					 		}
						} ?>
				</div>
				<div id="rightbar-bottom">
					<div id="sitelinks">
						<?php /*foreach ( $this->data['sidebar']['bottom'] as $action ) {
					 		echo "<a id='" . $action['id'] . "' " .
					 			"href='" . htmlspecialchars( $action['href'] ) . "'>" .
					 			htmlspecialchars( $action['text'] ) . "</a>";
						}*/ ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="footer">
		<?php
		foreach ( $this->getFooterLinks() as $category => $links ) {
			$this->html( $category );
			$noskip = false;
			foreach ( $links as $link ) {
				echo '&ensp;';
				$this->html( $link );
				echo '&ensp;';
				$noskip = true;
			}
			echo '<br />';
		}
		$footerIcons = $this->getFooterIcons( 'icononly' );
		if ( count( $footerIcons ) > 0 ) {
			$noskip = false;
			foreach ( $footerIcons as $blockName => $footerIcons ) {
				foreach ( $footerIcons as $icon ) {
					echo '&ensp;';
					echo $this->getSkin()->makeFooterIcon( $icon );
					echo '&ensp;';
				}
			}
		}
		?>
	</div>
	<?php $this->html( 'bottomscripts' );?>
<?php
	}
}