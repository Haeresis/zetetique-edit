<?php

class CodeEditorHooks {
	/**
	 * @param Title $title
	 * @param $model
	 * @param $format
	 * @return null|string
	 */
	static function getPageLanguage( Title $title, $model, $format ) {
		global $wgCodeEditorEnableCore;

		if ( $wgCodeEditorEnableCore ) {
			if ( $model === CONTENT_MODEL_JAVASCRIPT ) {
				return 'javascript';
			} elseif ( $model === CONTENT_MODEL_CSS ) {
				return 'css';
			} elseif ( $model === CONTENT_MODEL_JSON ) {
				return 'json';
			}
		}

		// Give extensions a chance
		// Note: $model and $format were added around the time of MediaWiki 1.28.
		$lang = null;
		Hooks::run( 'CodeEditorGetPageLanguage', [ $title, &$lang, $model, $format ] );

		return $lang;
	}

	/**
	 * @param $user
	 * @param $defaultPreferences
	 * @return bool
	 */
	public static function getPreferences( $user, &$defaultPreferences ) {
		$defaultPreferences['usecodeeditor'] = [
			'type' => 'api',
			'default' => '1',
		];
		return true;
	}

	/**
	 * @param EditPage $editpage
	 * @param OutputPage $output
	 * @return bool
	 */
	public static function editPageShowEditFormInitial( $editpage, $output ) {
		$output->addModuleStyles( 'ext.wikiEditor.toolbar.styles' );

		$title = $editpage->getContextTitle();
		$model = $editpage->contentModel;
		$format = $editpage->contentFormat;

		$lang = self::getPageLanguage( $title, $model, $format );
		if ( $lang && $output->getUser()->getOption( 'usebetatoolbar' ) ) {
			$output->addModules( 'ext.codeEditor' );
			$output->addJsConfigVars( 'wgCodeEditorCurrentLanguage', $lang );
		} elseif ( !ExtensionRegistry::getInstance()->isLoaded( "WikiEditor" ) ) {
			throw new ErrorPageError( "codeeditor-error-title", "codeeditor-error-message" );
		}
		return true;
	}
}
