<?php
/**
 *
 * @author Ivo Kund <ivo@opus.ee>
 * @date 22.01.14
 */

namespace opus\payment\widgets;

use opus\payment\services\payment\Form;
use yii\base\Widget;
use Yii;
use yii\helpers\Html;

/**
 * Widget for rendering payment buttons. Extend this form and override any methods you need
 *
 * @author Ivo Kund <ivo@opus.ee>
 * @package opus\payment\widgets
 */
class PaymentWidget extends Widget
{
    /**
     * @var bool
     */
    public $debug = false;

    /**
     * @var Form[]
     */
    public $forms;

    /**
     * @inheritdoc
     */
    public function run()
    {
        foreach ($this->forms as $form) {
            echo $this->generateForm($form);
        }
    }

    /**
     * @param Form $form
     * @return string
     */
    protected function beginForm(Form $form)
    {
        return Html::beginTag(
            'form',
            [
                'method' => 'post',
                'action' => $this->form->getAction(),
                'accept-charset' => $this->form->getCharset()
            ]
        );
    }

    /**
     * @param Form $form
     * @return string
     */
    protected function generateElements(Form $form)
    {
        $elements = '';
        foreach ($this->form as $param => $value)
        {
            $elements .= $this->generateElement($param, $value) . "\n";
        }
        return $elements;
    }

    /**
     * @param Form $form
     * @return string
     */
    protected function endForm(Form $form)
    {
        return Html::endTag('form');
    }

    /**
     * @param string $param
     * @param string $value
     * @return string
     */
    protected function generateElement($param, $value)
    {
        $value = mb_convert_encoding($value, 'utf-8', $this->form->getCharset());
        $method = $this->debug ? 'textInput' : 'hiddenInput';
        return Html::$method($param, $value);
    }

    /**
     * @param Form $form
     * @return string
     */
    protected function generateSubmit(Form $form)
    {
        return Html::submitButton($this->form->getProviderName());
    }

    /**
     * @return string
     */
    private function generateForm(Form $form)
    {
        return $this->beginForm($form) . $this->generateElements($form) . $this->generateSubmit($form) .  $this->endForm($form);
    }
}
