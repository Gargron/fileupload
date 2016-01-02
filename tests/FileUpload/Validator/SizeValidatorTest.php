<?php

namespace FileUpload\Validator;


use FileUpload\File;

class SizeValidatorTest extends \PHPUnit_Framework_TestCase
{
	protected $directory;
	protected $validator;
	protected $file;

	protected function setUp()
	{
		$this->directory = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;

		$_FILES['file'] = array(
			"name" => "real-image.jpg",
			"tmp_name" => $this->directory . 'real-image.jpg',
			"size" => 11.3,
			"error" => 0
		);

		$this->file = new File();
	}

	public function testNumericMaxSize()
	{
		$this->validator = new SizeValidator(pow(1024, 3));

		$this->file->size = 3499.4;

		$this->assertTrue($this->validator->validate($this->file, $_FILES['file']['size']));
	}

	public function testBetweenMinAndMaxSize()
	{

		$this->validator = new SizeValidator("40KB", "10KB");
		$this->file->size = 30.3;

		$this->assertTrue($this->validator->validate($this->file, $_FILES['file']['size']));
	}


	public function testFileSizeTooLarge()
	{
		$this->validator = new SizeValidator("20KB", 10);
		$this->file->size = 30.3;

		$this->assertFalse($this->validator->validate($this->file), $_FILES['file']['size']);
	}

	public function testFileSizeTooSmall()
	{
		$this->validator = new SizeValidator("1M", "40KB");
		$this->file->size = 30.3;

		$this->assertFalse($this->validator->validate($this->file), $_FILES['file']['size']);
	}

	public function testSetMaximumErrorMessages()
	{
		$this->validator = new SizeValidator("40KB", "10KB");
		$this->file->size = 50;

		$fileTooLarge = "Too Large";

		$this->validator->setErrorMessages(array(
			0 => $fileTooLarge
		));

		$this->validator->validate($this->file, $_FILES['file']['size']);

		$this->assertEquals($fileTooLarge, $this->file->error);
	}

	public function testSetMinimumErrorMessages()
	{
		$this->validator = new SizeValidator("40KB", "35KB");
		$this->file->size = $_FILES['file']['size'];

		$fileTooSmall = "Too Small";

		$this->validator->setErrorMessages(array(
			1 => $fileTooSmall
		));


		$this->validator->validate($this->file, $_FILES['file']['size']);

		$this->assertEquals($fileTooSmall, $this->file->error);
	}

	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Invalid File Max_Size
	 */
	public function testInvalidMaximumFileSize()
	{
		$this->validator = new SizeValidator("40A", 3);

		$this->file->size = $_FILES['file']['size'];

		$this->validator->validate($this->file, $_FILES['file']['size']);
	}

	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Invalid File Min_Size
	 */
	public function testInvalidMinimumFilesSize()
	{
		$this->validator = new SizeValidator("40KB", "-3");

		$this->file->size = $_FILES['file']['size'];

		$this->validator->validate($this->file, $_FILES['file']['size']);
	}

}
