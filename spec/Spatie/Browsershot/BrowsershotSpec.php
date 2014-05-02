<?php

namespace spec\Spatie\Browsershot;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;



class BrowsershotSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Spatie\Browsershot\Browsershot');
    }

    function it_should_fail_if_target_file_is_not_set()
    {
        $this->shouldThrow(new \Exception('targetfile not set'))->during('save', ['']);
    }



    function it_should_fail_if_invalid_url_is_set()
    {
        $this
            ->setURL('gibberish')
            ->shouldThrow(new \Exception('url is invalid'))
            ->during('save', [$this->getTestPath()]);
    }

    function it_should_fail_if_target_file_not_is_image()
    {
        $this
            ->setURL($this->getTestURL())
            ->shouldThrow(new \Exception('targetfile extension not valid'))
            ->during('save', [$this->getTestPath() . 'txt']);
    }

    function it_should_fail_if_binary_does_not_exist()
    {
        $this
            ->setURL($this->getTestURL())
            ->setBinPath('')
            ->shouldThrow(new \Exception('binary does not exist'))
            ->during('save', [$this->getTestPath()]);
    }

    /*
     * still figuring out how this test can be run on Travis CI
     *
    function it_should_create_an_image_if_run_succesfully()
    {
        $this
            ->setURL($this->getTestURL())
            ->save($this->getTestPath());

        $this->shouldExist($this->getTestPath());
    }
    */


    public function getTestPath()
    {
        return 'image.png';
    }

    public function getTestURL()
    {
        return 'http://google.com';
    }


    public function getMatchers()
    {
        return [
            'exist' => function($subject, $file) {
                    return file_exists($file);
                },
        ];
    }


}
