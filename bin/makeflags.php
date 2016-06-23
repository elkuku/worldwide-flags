<?php

/**
 * Created by IntelliJ IDEA.
 * User: elkuku
 * Date: 22.06.16
 * Time: 07:32
 */
class MakeFlags
{
	private $baseDir = '';

	public function __construct($basePath = '')
	{
		$this->baseDir = realpath($basePath ?: __DIR__ . '/../borderless_16x10');
	}

	public function create()
	{
		$fileData = $this->fillArrayWithFileNodes(new DirectoryIterator($this->baseDir));

		ksort($fileData);

		$fileList = [];

		/* @var SplFileInfo object */
		foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->baseDir)) as $name => $object)
		{
			if ($object->isDir())
			{
				continue;
			}

			$fileList[] = str_replace($this->baseDir . '/', '', $name);
		}

		sort($fileList);

		$flagWidth = 16;
		$flagHeight = 10;
		$imagesPerRow = 50;
		$flags = ['-verbose'];

		$cssLines = ['.flag {',
			'	width: ' . $flagWidth . 'px !important;',
			'	height: ' . $flagHeight . 'px !important;',
			'	background:url(../img/flags.png) no-repeat',
			'}',
			''
		];

		$resultImageFile = __DIR__ . '/../www/img/flags.png';

		// See: https://www.imagemagick.org/Usage/montage/
		$command = sprintf(
			'cd %s && montage %s -tile %sx -geometry +0+0 %s %s',
			$this->baseDir,
			'"' . implode('" "', $fileList) . '"',
			$imagesPerRow,
			implode(' ', $flags),
			$resultImageFile
		);

		$colCount = 0;
		$rowCount = 0;

		foreach ($fileList as $fileName)
		{
			$name = str_replace(['/', ' ', "'", '(', ')', ','], '-', $fileName);
			$name = str_replace('.png', '', $name);

			$xPos = $colCount ? '-' . $colCount * $flagWidth . 'px' : '0';
			$yPos = $rowCount ? '-' . $rowCount * $flagHeight . 'px' : '0';

			$cssLines[] = sprintf('.flag.flag-%s {background-position: %s %s}', $name, $xPos, $yPos);

			$colCount++;

			if ($colCount >= $imagesPerRow)
			{
				$colCount = 0;
				$rowCount++;
			}
		}

		//echo "\n" . $command . "\n";

		system($command, $ret);

		echo $ret;
		echo "\n";

		$jsonRoots = [];
		$jsonChildren = [];

		foreach ($fileData as $index => $items)
		{
			// Add a root dir
			$jsonRoots[] = '{ "id" : "' . $index . '", "parent" : "#", "text" : "' . $index . '" }';

			foreach ($items as $subdir => $item)
			{
				if (is_array($item))
				{
					// Add a sub dir
					$jsonRoots[] = '{ "id" : "' . $index . '/' . $subdir . '", "parent" : "' . $index . '", "text" : "' . $subdir . '" }';

					foreach ($item as $v)
					{
						// Add a file
						$cName = str_replace('.png', '', $v);
						$cssName = str_replace(' ', '-', $cName);
						$jsonChildren[] = sprintf(
							'{ "id" : "%s", "parent" : "%s", "text" : "%s", "icon": false }',
							$index . '/' . $subdir .'/' . $v,
							$index . '/' . $subdir,
							"<img  src='img/1x1.png' class='flag flag-$index-$subdir-$cssName'></img> $cName"
						);
					}
				}
				else
				{
					// Add a file
					$cName = str_replace('.png', '', $item);
					$cssName = str_replace(' ', '-', $cName);

					$jsonChildren[] = sprintf(
						'{ "id" : "%s", "parent" : "%s", "text" : "%s", "icon": false }',
						$index . '/' . $item,
						$index,
						"<img  src='img/1x1.png' class='flag flag-$index-$cssName'></img> $cName"
					);
				}
			}
		}

		file_put_contents(__DIR__ . '/../www/css/flags.css', implode("\n", $cssLines));
//		file_put_contents(__DIR__ . '/../www/flags_roots.json', "[\n" . implode(",\n", $jsonRoots) . "\n]");
//		file_put_contents(__DIR__ . '/../www/flags_children.json', "[\n" . implode(",\n", $jsonChildren) . "\n]");
		file_put_contents(__DIR__ . '/../www/flags.json', "[\n" . implode(",\n", array_merge($jsonRoots, $jsonChildren)) . "\n]");

		return;
	}

	private function fillArrayWithFileNodes(DirectoryIterator $dir)
	{
		$data = array();
		foreach ($dir as $node)
		{
			if ($node->isDir() && !$node->isDot())
			{
				$data[$node->getFilename()] = $this->fillArrayWithFileNodes(new DirectoryIterator($node->getPathname()));
			}
			else if ($node->isFile())
			{
				$data[] = $node->getFilename();
			}
		}

		return $data;
	}
}

(new MakeFlags())->create();