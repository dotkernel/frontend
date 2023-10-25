<?php

declare(strict_types=1);

namespace FrontendTest\Common;

use Laminas\Form\ElementInterface;
use Laminas\Form\FormInterface;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilterInterface;

use function count;
use function is_array;

trait FormTrait
{
    protected function formWillInstantiate(string $class): void
    {
        $this->assertInstanceOf($class, new $class());
        $this->assertInstanceOf($class, new $class(null, []));
        $this->assertInstanceOf($class, new $class('form'));
        $this->assertInstanceOf($class, new $class('form', []));
    }

    protected function formHasElements(FormInterface $form, array $elements = []): void
    {
        $this->assertEquals(count($elements), $form->count());

        foreach ($elements as $element) {
            $this->assertTrue($form->has($element));
            $this->assertInstanceOf(ElementInterface::class, $form->get($element));
        }
    }

    public function formHasInputFilter(InputFilterInterface $inputFilter, array $inputs = []): void
    {
        $this->assertCount(count($inputs), $inputFilter->getInputs());

        foreach ($inputs as $key => $input) {
            if (is_array($input)) {
                foreach ($input as $innerInput) {
                    $this->assertTrue($inputFilter->has($key));
                    $innerInputFilter = $inputFilter->get($key);
                    $this->assertTrue($innerInputFilter->has($innerInput));
                    $this->assertInstanceOf(Input::class, $innerInputFilter->get($innerInput));
                }
            } else {
                $this->assertTrue($inputFilter->has($input));
                $this->assertInstanceOf(Input::class, $inputFilter->get($input));
            }
        }
    }
}
