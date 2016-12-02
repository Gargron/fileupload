<?php

namespace FileUpload\Validator;

use FileUpload\File;

class SimpleTest extends \PHPUnit_Framework_TestCase
{
	public function testExceedSize()
	{
		$validator = new Simple(10, array());
		$file = new File;
		$file->size = 11;

		$this->assertFalse($validator->validate($file, 11));
		$this->assertNotEmpty($file->error);
	}

	public function testExceed1MSize()
	{
		$validator = new Simple("1M", array());
		$file = new File;
		$file->size = 1048577;

		$this->assertFalse($validator->validate($file, 11));
		$this->assertNotEmpty($file->error);
	}

	/**
	 * @expectedException \Exception
	 */
	public function testFailMaxSize1A()
	{
		$validator = new Simple("1A", array());
	}

	public function testWrongMime()
	{
		$validator = new Simple(10, array('image/png'));
		$file = new File;
		$file->type = 'application/json';

		$this->assertFalse($validator->validate($file, 11));
		$this->assertNotEmpty($file->error);
	}

	public function testOk()
	{
		$validator = new Simple(10, array('image/png'));
		$file = new File;
		$file->size = 10;
		$file->type = 'image/png';

		$this->assertTrue($validator->validate($file, 10));
		$this->assertEmpty($file->error);
	}

    public function testSetErrorMessages()
    {
        $file = new File();
        $file->type = "text/plain";
        $file->size = 10;

        $validator = new Simple(10, array('image/png'));

        $errorMessage = "Invalid file size";

        $validator->setErrorMessages(array(
            0 => $errorMessage
        ));

        $validator->validate($file, $file->size);

        $this->assertEquals($errorMessage, $file->error);
    }
}
