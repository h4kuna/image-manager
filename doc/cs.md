Image Manager
=============

Knihovna slouží pro správu obrázků a generování náhledů podle potřeby. Uživatel nahraje obrázek, kde originál se uloži do neveřejné části a pak podle potřeby si necháte vyrobit miniatury.

Instalace do projektu přes composer.
```sh
$ composer require h4kuna/image-manager
```

Registrace v NEONu
------------------
```sh
extensions:
	imageManager: h4kuna\ImageManager\DI\ImageExtension

imageManager:
	upload:
		sourcePath: %appDir%/../upload # Privátní část webu kam se uloží originální obrázky
		maxResolution: 1500x1500 # všechny nahrané obrázky budou automaticky zmenšeny (defaultně 3840×2160)
	public:
		tempUrl: temp/images # cesta od basePath k obrázkům
		tempDir: %wwwDir%/temp/images # cesta na filesystemu k obrázkům kam se mají zmenšovat
		allowedResolutions: # povolená rozlišení (volitelné)
			- 1000x1000
			- 300x200
	remoteSource: (volitelné)
		domain: # vyplňte adresu produkčního serveru a obrázky se od tamtud stáhnou
	shortcuts: (volitelné)
		cutBig: 3 # Nette\Utils\Image::STRETCH | Nette\Utils\Image::SHRINK_ONLY
```

Doporučuji omezit počet rozlišení, pomocí **allowedResolutions:**, ať se netvoří něco co nechcete a nebo ať lidi nezkoušejí co server umí.

Zpracování obrázů má zkratky, které vkládáte do šablony a obrázek se ořízně, roztáhne nebo nic neudělá, jsou definované základní [v makru](../src/Template/LatteMacro.php). Pomocí metody **shortcuts** si můžete přidat vlastní nebo původní přenastavit. Je to číslo, bitový součet konstatnt. Zkratka **default** se použije pokud ji nevyplníte v šabloně.

Služby, které budete potřebovat
======

[saver](../src/Saver.php)
-----
Použijte pro nahrání obrázků.
```php
try {
    $image = $saver->saveFileUpload($fileUpload, 'volitelny/podadresar');
} catch (\h4kuna\ImageManager\BadFileUploadException $e) {
    // soubor je špatný
}


dump($image->getRelativePath()); 
```

další metody **save()** a **saveImage()** dělají to samé jen přijímají jiné parametry například cestu k souboru nebo [Nette Image](https://api.nette.org/Nette.Utils.Image.html).

Návratový objekt je [Image](../src/Image.php), který nám poskytuje relativní cestu pomocí **getRelativePath()**, kterou budeme potřebovat pro vytváření miniatur, tak si ji uložme. Poskytuje i šiřku a výšku obrázku nebo velikost souboru přes \SplFileInfo.

[imageView](../src/ImageView.php)
---------
Bude se hodit vyrobit si na servru .htaccess. Když server nenajde obrázek, tak přesměruje požaavek na tento soubor, který vytvoří náhled. A při dalším stejném requestu obrázek najde.

```php
$container = require 'bootstrap.php';
// z containeru vytáhneme imageView
// rozparsujeme url abychom věděli jaký obrázek chybí
if(!$imageView->send($name, $resolution, $method)) {
    // nastavíme 404 hlavičku
}
// obrázek se zobrazí a příšttě se už ukáže na požadované cestě, která nabyla k dispozici.
```

### Pouziti v latte
Nainstaluje se makro {img}, které použijete v šablonách. Pokud chcete aplikovat na obrázek více pravidel pro zpracování tak je odděltě středníkem (;). Není potřeba obalovat do uvozovek.

```html
<a href="{img $imageRelativePath, 300x200, fill;shrink}">
```
Zápis pomocí n makra. Třetí parametr není povinný.
```html
<a href="{img $imageRelativePath, '300x200'}"><img n:img="$imageRelativePath, '300x200', 'fill,shrink'"></a>
```

výstup:

```html
<a href="/temp/images/300x200-0/hashimage123.jpg"><img src="/temp/images/300x200-5/hashimage123.jpg"></a>
```
