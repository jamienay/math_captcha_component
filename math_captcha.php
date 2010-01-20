<?php
/**
 * Math Captcha Component class.
 *
 * Generates a simple, plain text math equation as an alternative to image-based CAPTCHAs.
 *
 * @filesource
 * @author			Jamie Nay
 * @copyright       Jamie Nay
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link            http://jamienay.com/code/math-captcha-component
 */
class MathCaptchaComponent extends Object {

    /**
     * Other components needed by this component
     *
     * @access public
     * @var array
     */
    public $components = array('Session');
    
    /**
	 * component settings
	 * 
	 * @access public
	 * @var array
	 */
	public $settings = array();
	
    /**
	 * Default values for settings.
	 *
	 * @access private
	 * @var array
	 */
    private $__defaults = array(
        'operand' => '+',
        'minNumber' => 1,
        'maxNumber' => 5,
        'numberOfVariables' => 2
    );
    
    /**
     * The variables used in the equation.
     *
     * @access public
     * @var array
     */
    public $variables = array();
    
    /*
     * The math equation.
     *
     * @access public
     * @var string
     */
    public $equation = null;
    
    /**
     * Configuration method.
     *
     * @access public
     * @param object $model
     * @param array $settings
     */
    public function initialize(&$controller, $settings = array()) {
        $this->settings = array_merge($this->__defaults, $settings);
    }

    /*
     * Method that generates a math equation based on the component settings. It also calls
     * a secondary function, registerAnswer(), which determines the answer to the equation
     * and sets it as a session variable.
     *
     * @access public
     * @return string
     * 
     */
    public function generateEquation() {
        // Loop through our range of variables and set a random number for each one.
        foreach (range(1, $this->settings['numberOfVariables']) as $variable) {
            $this->variables[] = rand($this->settings['minNumber'], $this->settings['maxNumber']);
        }
        
        $this->equation = implode(' ' . $this->settings['operand'] . ' ', $this->variables);
        // This function determines the answer to the equation and stores it as a session variable.
        $this->registerAnswer();

        return $this->equation;
    }
    
    /*
     * Determines the answer to the math question from the variables set in generateEquation()
     * and registers it as a session variable.
     *
     * @access public
     * @return integer
     */
    public function registerAnswer() {
        // The eval() function gives us the $answer variable.
        eval("\$answer = ".$this->equation.";");
        
        $this->Session->write('MathCaptcha.answer', $answer);
        
        return $answer;
    }
    
    /*
     * Compares the given data to the registered equation answer.
     * 
     * @access public
     * @return boolean
     */
    public function validates($data) {
        return $data == $this->Session->read('MathCaptcha.answer');
    }
    
}

?>