<?php
class PublishablePackage {
	private $config;

	public function __construct($options) {
		$this->config = $options;
	}

	public function pack($gzpath, $zpath) {
		$this->copyFiles();
		$this->writeMetadata();
		$this->gzipAll($gzpath);
		$this->zipAll($zpath);
		$this->clearTempFolder();
	}

	private function fetchFileList() {
		$files = [];

		foreach ($this->config['contents'] as $domain) {
			foreach ($domain->getFiles() as $file) {
				$files[] = $domain->getRoot().$file;
			}
		}

		return $files;
	}

	private function writeMetadata() {
		$metadata = new Metadata($this->config);
		$metadata->save('.tmp/package.xml');
	}

	private function clearTempFolder() {
		exec('rm -rf .tmp');
	}

	private function copyFiles() {
		foreach ($this->fetchFileList() as $path) {
			@mkdir(dirname('.tmp/'.$path), 0777, true);
			copy($path, '.tmp/'.$path);
		}
	}

	private function gzipAll($path) {
		$name = 'scripts/packager_work.tmp';
		$data = new PharData($name);
		$data->buildFromDirectory(realpath('.tmp'));
		$data->compress(Phar::GZ);
		unlink($name);
		$this->moveResultingFile($path);
	}

	private function moveResultingFile($path) {
		rename('scripts/packager_work.tar.gz', $path);
	}

	private function zipAll($path) {
		system("cd .tmp && zip -rq ../magento-gateway-ebanx.zip ./* && cd ..");
	}
}

class FileDomain {
	private static $TYPE_PATHS = [
		self::TYPE_COMMUNITY => 'app/code/community/',
		self::TYPE_DESIGN => 'app/design/',
		self::TYPE_ETC => 'app/etc/',
		self::TYPE_OTHER_WEB => './',
		self::TYPE_LIB => 'lib/',
		self::TYPE_SKIN => 'skin/',
	];

	const TYPE_COMMUNITY = 'magecommunity';
	const TYPE_DESIGN = 'magedesign';
	const TYPE_ETC = 'mageetc';
	const TYPE_OTHER_WEB = 'mageweb';
	const TYPE_LIB = 'magelib';
	const TYPE_SKIN = 'mageskin';

	private $type;
	private $files = [];

	public function __construct($type) {
		$this->type = $type;
	}

	public function getType() {
		return $this->type;
	}

	public function getRoot() {
		return self::$TYPE_PATHS[$this->type];
	}

	public function addDir($path) {
		$searchPath = $this->getRoot().$path;

		$dir = opendir($searchPath);
		while (($leaf = readdir($dir)) !== false) {
			if (strpos($leaf, '.') === 0)
				continue;

			if (is_dir($searchPath.'/'.$leaf)) {
				$this->addDir($path.'/'.$leaf);
				continue;
			}

			$this->files[] = $path.'/'.$leaf;
		}
	}

	public function addFile($path) {
		$this->files[] = $path;
	}

	public function getFiles() {
		return $this->files;
	}
}

class Metadata {
	private $config;
	private $document;
	private $properties = null;

	public function __construct($config) {
		$this->config = $config;

		$this->properties = array_replace_recursive([
			'name' => '',
			'version' => '',
			'stability' => '',
			'license' => '',
			'channel' => '',
			'extends' => '',
			'summary' => '',
			'description' => '',
			'notes' => '',
			'authors' => [
				[
					'name' => '',
					'user' => '',
					'email' => '',
				],
			],
			'date' => '',
			'time' => '',
			'compatible' => '',
			'dependencies' => [
				'required' => [
					'php' => [
						'min' => '',
						'max' => '',
					],
				],
			],
		], $this->config);

		date_default_timezone_set('America/Sao_Paulo');
		$this->properties['date'] = date('Y-m-d');
		$this->properties['time'] = date('H:i:s');
	}

	public function save($filename) {
		$contents = $this->config['contents'];
		unset($this->properties['contents']);

		$this->document = new XMLWriter();
		$this->document->openMemory();
		$this->document->startDocument('1.0');
		$this->document->startElement('package');
		$this->writeHeader();
		$this->writeContents($contents);
		$this->document->endElement();
		$this->document->endDocument();
		file_put_contents('.tmp/package.xml', $this->document->outputMemory());
	}

	private function writeHeader() {
		$this->writeRecursive($this->properties, 'headers');
	}

	private function writeRecursive($thing, $key = 'elements') {
		if (is_array($thing) && !is_numeric(array_keys($thing)[0])) {
			$this->writeObject($thing, $key);
		} elseif (is_array($thing)) {
			$this->writeArray($thing, $key);
		} else {
			if (is_object($thing)) {
				echo "Attempting to print object as text\n";
				exit;
			}
			$this->document->text($thing);
		}
	}

	private function writeObject($object) {
		foreach ($object as $key => $value) {
			$this->document->startElement($key);
			$this->writeRecursive($value, $key);
			$this->document->endElement();
		}
	}

	private function writeArray($children, $key) {
		$subkey = substr($key, 0, -1);
		foreach ($children as $value) {
			$this->document->startElement($subkey);
			$this->writeRecursive($value, $subkey);
			$this->document->endElement();
		}
	}

	private function writeContents($contents) {
		$this->document->startElement('contents');
		foreach ($contents as $domain) {
			$this->writeDomain($domain);
		}
		$this->document->endElement();
	}

	private function writeDomain(FileDomain $domain) {
		$tree = $this->parseDomainElementTree($domain);
		$this->writeDomainElementTree($tree, $domain->getType());
	}

	private function parseDomainElementTree(FileDomain $domain) {
		$tree = [];
		$count = 0;
		$depth = 0;
		$currentDepth = 0;
		foreach ($domain->getFiles() as $file) {
			$count++;
			$currentDepth = 1;

			$filename = basename($file);
			$parts = explode('/', $file);
			$last = &$tree;
			foreach ($parts as $part) {
				$value = [];
				if ($filename === $part) {
					$value = md5_file($domain->getRoot().$file);
				}

				if (!isset($last[$part])) {
					$last[$part] = $value;
				}

				$last = &$last[$part];
				$currentDepth++;
				$depth = max($currentDepth, $depth);
			}
		}

		$domainType = $domain->getType();
		echo "Parsed domain, type: $domainType,\tfiles: $count,\tdepth: $depth\n";

		return $tree;
	}

	private function writeDomainElementTree($tree, $type) {
		$this->document->startElement('target');
		$this->document->writeAttribute('name', $type);
		$this->writeDomainElementTreeDir($tree);
		$this->document->endElement();
	}

	private function writeDomainElementTreeDir($contents) {
		foreach ($contents as $key => $value) {
			if (is_array($value)) {
				$this->writeDomainElementTreeDirNode($key, $value);
				continue;
			}
			$this->writeDomainElementTreeFileNode($key, $value);
		}
	}

	private function writeDomainElementTreeDirNode($name, $contents) {
			$this->document->startElement('dir');
			$this->document->writeAttribute('name', $name);
			$this->writeDomainElementTreeDir($contents);
			$this->document->endElement();
	}

	private function writeDomainElementTreeFileNode($name, $hash) {
		$this->document->startElement('file');
		$this->document->writeAttribute('name', $name);
		$this->document->writeAttribute('hash', $hash);
		$this->document->endElement();
	}
}
