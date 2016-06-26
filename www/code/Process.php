<?php
/**
 * Created by IntelliJ IDEA.
 * User: elkuku
 * Date: 25.06.16
 * Time: 21:22
 */

class Process
{
	private $baseDir;

	public function __construct($baseDir)
	{
		$this->baseDir = $baseDir;
	}

	/**
	 * @param $commandName
	 *
	 * @return bool|stdClass
	 */
	public function processCommand($commandName)
	{
		$response = false;

		switch ($this->getRequestVar($commandName))
		{
			case 'build':

				$tStamp = time();

				$imageFileName = $this->baseDir . '/flags' . $tStamp . '.png';
				$cssFileName   = $this->baseDir . '/flags' . $tStamp . '.css';
				$zipFileName   = $this->baseDir . '/flags' . $tStamp . '.zip';

				$flagList = $this->getRequestVar('flags');

				// Do some hard coded filtering on file paths here
				$flagList = preg_replace('{[^\w\s\'.\-\/"]}x', '', $flagList);

				ob_start();

				$flagsProcessedNum = (new MakeFlags())
					->create($flagList, $imageFileName, $cssFileName);

				// No more error producing codes below here !!!

				$error = ob_get_clean();

				$response = new stdClass;

				$response->message = sprintf('%d Flag Images processed.', $flagsProcessedNum);
				$response->error   = $error;
				$response->css     = '';
				$response->image   = '';
				$response->zipFile = '';

				$errors = [];

				if (file_exists($cssFileName))
				{
					$response->css = implode('', file($cssFileName));
				}
				else
				{
					$errors[] = 'Css file could not be created.';
				}

				if (file_exists($imageFileName))
				{
					$response->image = base64_encode(file_get_contents($imageFileName));
				}
				else
				{
					$errors[] = 'Image file could not be created.';
				}

				if (!$errors)
				{
					try
					{
						$this->createZip($imageFileName, $cssFileName, $zipFileName);

						$response->zipFile = (str_replace($this->baseDir . '/', '', $zipFileName));
					}
					catch (Exception $exception)
					{
						$errors[] = $exception->getMessage();
					}
				}

				break;
		}

		return $response;
	}

	public function createZip($imageFile, $cssFile, $zipFile)
	{
		$zip = new ZipArchive();

		if (true !== $zip->open($zipFile, ZipArchive::CREATE))
		{
			throw new DomainException('Can not open ' . $zipFile);
		}

		$zip->addFile($imageFile, 'flags.png');
		$zip->addFile($cssFile, 'flags.css');

		//echo "numfiles: " . $zip->numFiles . "\n";
		//echo "status:" . $zip->status . "\n";

		$zip->close();
	}

	public function getRequestVar($name)
	{
		$value = isset($_GET[$name]) ? $_GET[$name] : '';

		if (!$value)
		{
			$value = isset($_POST[$name]) ? $_POST[$name] : '';
		}

		return $value;
	}
}
