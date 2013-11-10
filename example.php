<?php

use Nette\Diagnostics\Debugger;

include __DIR__ . "/vendor/autoload.php";

$configurator = new Nette\Config\Configurator;
$pathTmp = '/tests/tmp';
$imageName = 'avatar.jpg';
$tmp = __DIR__ . $pathTmp;
$configurator->enableDebugger($tmp);
$configurator->setTempDirectory($tmp);

$configurator->onCompile[] = function ($configurator, $compiler) use ($pathTmp) {
    $ext = new h4kuna\DI\ImageExtension();
    $ext->defaults['namespace'] = array(
        'small' => array('80x80'),
        'big' => array('150x150', 'exact')
    );
    $ext->defaults['noImage'] = '../noImage.jpg'; // relative to $pathTmp
    // $ext->defaults['domain'] = 'http://example.com';
    $ext->defaults['source'] = $pathTmp;
    $ext->defaults['test'] = TRUE;
    $compiler->addExtension('imageExtension', $ext);
};

$container = $configurator->createContainer();



/**
 * h4kuna\Pathnizer example
 */
$pathnizer = new h4kuna\Pathnizer(__DIR__, $container->httpRequest);
$pathnizer->append($pathTmp . '/milan'); // append to path
$pathnizer->checkSetup(); // only control paths
echo $pathnizer->buildFs('/css/', '/screen.css') . "<br>\n";
echo $pathnizer->buildFs('/css', 'screen.css') . "<br>\n";
echo $pathnizer->buildFs('css', 'screen.css') . "<br>\n";
echo $pathnizer->buildFs('css/screen.css') . "<br>\n";
echo $pathnizer->buildFs('../css/screen.css') . "<br>\n";
echo $pathnizer->buildFs('./css/screen.css') . "<br>\n";
echo $pathnizer->buildFs('css/screen') . "<br>\n";
echo $pathnizer->buildFs('css/screen/') . "<br>\n";
// add basePath from Request
echo $pathnizer->buildUrl('/css/screen.css') . "<br>\n";
echo $pathnizer->buildUrl('css/screen.css') . "<br>\n";
echo $pathnizer->buildUrl('//css/screen.css') . "<br>\n";


/**
 * h4kuna\Image\Container example
 */
$imageManager = Nette\Framework::VERSION == '2.1-dev' ?
        $container->createServiceImageExtension__imageManager() :
        $container->imageExtension->imageManager;

$image = $imageManager->createImage($imageName);

$img = $image->create(150, 150);
$imgMini = $img->create(66, 80, \Nette\Image::EXACT);

$img2 = $imageManager->saveImage($img->getPathname(), 'save-to-dir');
echo $img2->getFilename(); // save-to-dir/avatar.xxx.jpg use for save
echo $img2->render();

function imageInfo(h4kuna\Image\ImageSource $image, $title) {
    ?>
    <div style="border: 1px gray solid; margin: 10px;">
        <h2><?php echo $title; ?></h2>
        <table>
            <tr>
                <th>Image</th>
                <td><a href="<?php echo $image->getUrl() ?>" target="_blank"><?php echo $image->render(); ?></a></td>
            </tr>
            <tr>
                <th>Width</th>
                <td><?php echo $image->getWidth() ?>px</td>
            </tr>
            <tr>
                <th>Height</th>
                <td><?php echo $image->getHeight() ?>px</td>
            </tr>
        </table>
    </div>
    <?php
}

imageInfo($imgMini, 'Mini');

imageInfo($img, 'Middle');

imageInfo($image, 'Original');


/**
 * Use namespace
 */
echo $imageManager->setNamespace('small')->request($imageName)->render();
echo $imageManager->setNamespace('big')->request($imageName)->render();



