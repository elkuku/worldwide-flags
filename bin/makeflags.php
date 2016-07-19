<?php

/**
 * Created by IntelliJ IDEA.
 * User: elkuku
 * Date: 22.06.16
 * Time: 07:32
 */
class MakeFlags
{
	private $flagDir = '';

	private $fileData = [];
	private $fileList = [];

	public $flagWidth = 16;
	public $flagHeight = 10;
	public $imagesPerRow = 50;

	public function __construct($flagDir = '')
	{
		$this->flagDir = realpath($flagDir ? : __DIR__ . '/../borderless_16x10');
	}

	public function createAll()
	{
		$fileData = $this->fillArrayWithFileNodes(new DirectoryIterator($this->flagDir));

		ksort($fileData);

		/* @var SplFileInfo object */
		foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->flagDir)) as $name => $object)
		{
			if ($object->isDir())
			{
				continue;
			}

			$this->fileList[] = str_replace($this->flagDir . '/', '', $name);
		}

		sort($this->fileList);

		$this->createImage(__DIR__ . '/../www/img/flags.png', ['-verbose']);

		file_put_contents(__DIR__ . '/../www/css/flags.css', implode("\n", $this->createCss()));
		file_put_contents(__DIR__ . '/../www/flags.json', "[\n" . implode(",\n", $this->createJson()) . "\n]");

		return $this;
	}

	public function create($fileList, $resultImagePath, $resultCssPath)
	{
		$this->setFileList($fileList);

		$this->createImage($resultImagePath);

		file_put_contents($resultCssPath, implode("\n", $this->createCss()));

		return count($this->fileList);
	}

	/**
	 * @param mixed $fileList string or array
	 *
	 * @return MakeFlags
	 */
	public function setFileList($fileList)
	{
		$this->fileList = is_array($fileList) ? $fileList : explode('" "', trim($fileList, '"'));

		$this->imagesPerRow = sqrt(count($this->fileList));

		return $this;
	}

	private function createCss()
	{
		$colCount = 0;
		$rowCount = 0;

		$cssLines = ['.flag {',
			'	width: ' . $this->flagWidth . 'px;',
			'	height: ' . $this->flagHeight . 'px;',
			'	background:url(flags.png) no-repeat',
			'}',
			''
		];

		foreach ($this->fileList as $fileName)
		{
			$name = str_replace(['/', ' ', "'", '(', ')', ','], '-', $fileName);
			$name = str_replace('.png', '', $name);

			$xPos = $colCount ? '-' . $colCount * $this->flagWidth . 'px' : '0';
			$yPos = $rowCount ? '-' . $rowCount * $this->flagHeight . 'px' : '0';

			$cssLines[] = sprintf('.flag.flag-%s {background-position: %s %s}', $name, $xPos, $yPos);

			$colCount++;

			if ($colCount >= $this->imagesPerRow)
			{
				$colCount = 0;
				$rowCount++;
			}
		}

		return $cssLines;
	}

	/**
	 * Create the combined image.
	 *
	 * @param   string  $resultImageFile  Path to result file.
	 * @param   array   $flags            List with flag names.
	 *
	 * @return $this
	 */
	private function createImage($resultImageFile, array $flags = [])
	{
		$fileList = '"' . implode('" "', $this->fileList) . '"';

		// See: https://www.imagemagick.org/Usage/montage/
		$command = sprintf(
			'cd %s && montage %s -tile %sx -geometry +0+0 %s %s',
			$this->flagDir,
			$fileList,
			$this->imagesPerRow,
			implode(' ', $flags),
			$resultImageFile
		);

		$lastLine = system($command, $ret);

		if ($ret)
		{
			echo $lastLine;
		}

		return $this;
	}

	private function createJson()
	{
		$jsonRoots = [];
		$jsonChildren = [];

		foreach ($this->fileData as $index => $items)
		{
			// Add a root dir
			$jsonRoots[] = '{ "id" : "' . $index . '", "parent" : "#", "text" : "' . $index . '" }';

			foreach ($items as $subDir => $item)
			{
				if (is_array($item))
				{
					// Add a sub dir
					$jsonRoots[] = '{ "id" : "' . $index . '/' . $subDir . '", "parent" : "' . $index . '", "text" : "' . $subDir . '" }';

					foreach ($item as $v)
					{
						// Add a file
						$cName = str_replace('.png', '', $v);
						$cssName = str_replace(' ', '-', $cName);
						$jsonChildren[] = sprintf(
							'{ "id" : "%s", "parent" : "%s", "text" : "%s", "icon": false }',
							$index . '/' . $subDir .'/' . $v,
							$index . '/' . $subDir,
							"<img  src='img/1x1.png' class='flag flag-$index-$subDir-$cssName'></img> $cName"
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

		return array_merge($jsonRoots, $jsonChildren);
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

if ('cli' == php_sapi_name())
{
	(new MakeFlags())->createAll();
}
