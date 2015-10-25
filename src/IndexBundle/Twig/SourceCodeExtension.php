<?php

namespace IndexBundle\Twig;

class SouceCodeExtension extends \Twig_Extension
{
	protected $loader;
	protected $controller;
	protected $template;
	protected $kernelRootDir;

	public function __construct(\Twig_LoaderInterface $loader, $kernelRootDir)
	{
		$this->kernelRootDir = $kernelRootDir;
		$this->loader = $loader;
	}

	public function setController($controller)
	{
		$this->controller = $controller;
	}

	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('show_source_code', array($this, 'showSourceCode'), array('is_safe' => array('html'), 'need_environment' => true)),
		);
	}

	public function showSourceCode(\Twig_Environment $twig, $template)
	{
		$this->template = $template;

		return $twig->render('default/_source_code.html.twig', array(
			'controller' => $this->getController(),
			'template' => $this->getTemplate(),
		));
	}

	private function getController()
	{
		if (null === $this->controller) {
			return;
		}

		$className = get_class($this->controller[0]);
		$class = new \ReflectionClass($className);
		$method = $class->getMethod($this->controller[1]);

		$classCode = file($class->getFilename());
		$methodCode = array_slice($classCode, $method->getStartLine() -1, $method->getEndLine() - $method->getStartLine() + 1);
		$controllerCode = '    '.$mehtod->getDocComment()."\n".implode('', $methodCode);

		return array(
			'file_path' => $class->getFilename(),
			'starting_line' => $method->getStartLine().
			'source_code' => $this->unindentCode($controllerCode)
		);
	}

	private function getTemplate()
	{
		$templateName = $this->template->getTemplateName();

		return array(
			'file_path' => $this->kernelRootDir.'Resources/views/'.$templateName,
			'starting_line' => 1,
			'source_code' => $this->loader->getSource($templateName),
		);
	}

	private function unindentCode($code)
	{
		$formattedCode = '';
		$codeLines = explode("\n", $code);

		$indentedLines = array_filter($codeLines, function ($lineOfCode) {
			return '' === $lineOfCode || '    ' === substr($lineOfCode, 0, 4);
		});

		if (count($indentedLines) === count($codeLines)) {
			$formattedCode = array_map(function ($lineOfCode) {
				return substr($lineOfCode, 4);
			}, $codeLines);

			$formattedCode = implode("\n", $formattedCode);
		} else {
			$formattedCode = $code;
		}

		return $formattedCode;
	}

	public function getName()
	{
		return 'app.source_code_extension';
	}
}