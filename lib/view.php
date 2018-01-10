<?php
// -------------------------------------------
// view.php
//
// Steven Mead 2016
// School of Computing
// Teesside University
//
//
// Notes:
// ------
// Not a proper View class.
//
// It's only purpose in the code harness is to encapsulate the
// render behaviour.
// -------------------------------------------

class View {

  public function __construct($controller) {
    $this->controller = $controller;
  }

  public function render($header,$output,$partial = false) {

    $html = "";

    if(!$partial) {
      $html .= <<<_1
        <!DOCTYPE html>
        <html>
          <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <title>{$this->controller->getTitle()}</title>
_1;
      $html .= <<<_DEFAULT_ASSETS
            <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
            <link rel="stylesheet" type="text/css" href="{$this->assetPath('css','main.css')}">
            <script type="text/javascript" src="//code.jquery.com/jquery-1.12.4.min.js"></script>
            <script type="text/javascript" src="//code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
            <script type="text/javascript" src="{$this->assetPath('js','main.js')}"></script>
_DEFAULT_ASSETS;

      $controllerAssets = $this->controller->getAssets();

      $cssAssets = $controllerAssets['css'];
      foreach($cssAssets as $cssAsset) {
        $html .= <<<_CSS_ASSETS
          <link rel="stylesheet" type="text/css" href="{$this->assetPath('css',$cssAsset)}">
_CSS_ASSETS;
    }

      $jsAssets = $controllerAssets['js'];
      foreach($jsAssets as $jsAsset) {
        $html .= <<<_JS_ASSETS
          <script type="text/javascript" src="{$this->assetPath('js',$jsAsset.'.js')}"></script>
_JS_ASSETS;
      }

      $html .= <<<_2
      </head>
    <body>
      <h1>{$header}</h1>
_2;

    }

    $html .= "<div>$output</div>";

    if(!$partial) {

        $html .= <<<_3
      </body>
    </html>
_3;
    }

    echo $html;
  }

  protected function assetPath($type,$file) {
    $request = $this->controller->getHostInfo();
    return "{$request['protocol']}{$request['host']}{$request['path']}{$type}/{$file}";
  }

}

?>
