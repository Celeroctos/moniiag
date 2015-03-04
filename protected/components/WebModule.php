<?php

abstract class WebModule extends CWebModule {

	/**
	 * Override that method to return paths to javascript,
	 * css and less files for current module
	 */
	public abstract function getClientScripts();

	/**
	 * Register module scripts
	 * @param $webModule - Module instance
	 * @throws CException
	 */
	public static function register($webModule) {
		if (!$webModule || !($webModule instanceof WebModule)) {
			return;
		}
		foreach ($webModule->getClientScripts() as $script) {
			$extension = self::getExtension($script);
			if ($extension == "less") {
				$src = Yii::app()->getBaseUrl()."/css/";
			} else {
				$src = Yii::app()->getBaseUrl()."/$extension/";
			}
			if ($extension == "js") {
				$src .= $webModule->getName()."/";
			}
			if (strpos($script, "/") !== false) {
				$src = $src.substr($script, 1);
			} else {
				$src = $src.$script;
			}
			$method = "render".$extension;
			if (method_exists("WebModule", $method)) {
				self::$method($src);
			} else
				throw new CException("Unresolved script type \"$extension\"");
			print "\r\n";
		}
	}

	/**
	 * Get script's extension
	 * @param string $script - Script filename
	 * @return string - Script's extension
	 */
	private static function getExtension($script) {
		if (($index = strrpos($script, ".")) !== false) {
			return substr($script, $index + 1);
		} else {
			return "";
		}
	}

	private static function renderJs($src) {
		print "<script type=\"text/javascript\" src=\"{$src}\"></script>";
	}

	private static function renderCss($src) {
		print "<link href=\"{$src}\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\"/>";
	}

	private static function renderLess($src) {
		print "<link href=\"{$src}\" rel=\"stylesheet\" type=\"text/less\" media=\"screen\"/>";
	}
}