<?php

namespace Wikibase;

use Language;
use Message;
use OutputPage;
use Wikimedia\Assert\Assert;

/**
 * Handles adding user-specific or other js config to OutputPage
 *
 * @license GPL-2.0+
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
class OutputPageJsConfigBuilder {

	/**
	 * @var CopyrightMessageBuilder
	 */
	private $copyrightMessageBuilder;

	public function __construct() {
		$this->copyrightMessageBuilder = new CopyrightMessageBuilder();
	}

	/**
	 * @param OutputPage $out
	 * @param string $rightsUrl
	 * @param string $rightsText
	 * @param string[] $badgeItems
	 *
	 * @return array
	 */
	public function build( OutputPage $out, $rightsUrl, $rightsText, array $badgeItems ) {
		$lang = $out->getLanguage();
		$title = $out->getTitle();

		Assert::parameter( $title !== null, '$out', 'Passed OutputPage needs to have a Title set' );

		$configVars = $this->getCopyrightConfig( $rightsUrl, $rightsText, $lang );

		$configVars['wbBadgeItems'] = $badgeItems;

		return $configVars;
	}

	/**
	 * @param string $rightsUrl
	 * @param string $rightsText
	 * @param Language $language
	 *
	 * @return array
	 */
	private function getCopyrightConfig( $rightsUrl, $rightsText, Language $language ) {
		$copyrightMessage = $this->getCopyrightMessage( $rightsUrl, $rightsText, $language );

		return $this->getCopyrightVar( $copyrightMessage, $language );
	}

	/**
	 * @param Message $copyrightMessage
	 * @param Language $language
	 *
	 * @return array[]
	 */
	private function getCopyrightVar( Message $copyrightMessage, Language $language ) {
		// non-translated message
		$versionMessage = new Message( 'wikibase-shortcopyrightwarning-version' );

		return [
			'wbCopyright' => [
				'version' => $versionMessage->parse(),
				'messageHtml' => $copyrightMessage->inLanguage( $language )->parse()
			]
		];
	}

	/**
	 * @param string $rightsUrl
	 * @param string $rightsText
	 * @param Language $language
	 *
	 * @return Message
	 */
	private function getCopyrightMessage( $rightsUrl, $rightsText, Language $language ) {
		global $wgEditSubmitButtonLabelPublish;

		$messageKey = ( $wgEditSubmitButtonLabelPublish ) ? 'wikibase-publish' : 'wikibase-save';
		$copyrightMessage = $this->copyrightMessageBuilder->build(
			$rightsUrl,
			$rightsText,
			$language,
			$messageKey
		);

		return $copyrightMessage;
	}

}
