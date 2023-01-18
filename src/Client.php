<?php declare(strict_types=1);

namespace NBrowserKit;

use Nette\Application\Application;
use Nette\Application\IPresenterFactory;
use Nette\Routing\Router;
use Nette\DI\Container;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Symfony\Component\BrowserKit;

/**
 * @method BrowserKit\Response getResponse()
 * @method IRequest getRequest()
 */
class Client extends BrowserKit\AbstractBrowser
{
	private ?Container $container = null;


	public function setContainer(Container $container): void
	{
		$this->container = $container;
	}


	protected function getContainer(): Container
	{
		if ($this->container === null) {
			throw new MissingContainerException('Container is missing, use setContainer() method to set it.');
		}

		return $this->container;
	}


	/**
	 * @param IRequest $request
	 * @throws MissingContainerException
	 */
	protected function doRequest($request): BrowserKit\Response
	{
		$container = $this->getContainer();

		$response = new Response;

		$container->removeService('httpRequest');
		$container->addService('httpRequest', $request);
		$container->removeService('httpResponse');
		$container->addService('httpResponse', $response);

		/** @var IPresenterFactory $presenterFactory */
		$presenterFactory = $container->getByType(IPresenterFactory::class);
		/** @var Router $router */
		$router = $container->getByType(Router::class);
		$application = $this->createApplication($request, $presenterFactory, $router, $response);
		$container->removeService('application');
		$container->addService('application', $application);

		ob_start();
		$application->run();
		$content = ob_get_clean();
		assert($content !== false);

		return new BrowserKit\Response($content, $response->getCode(), $response->getHeaders());
	}


	/**
	 * Filters the BrowserKit request to the `Nette\Http` one.
	 */
	protected function filterRequest(BrowserKit\Request $request): IRequest
	{
		return RequestConverter::convertRequest($request);
	}


	protected function createApplication(
		IRequest $request,
		IPresenterFactory $presenterFactory,
		Router $router,
		IResponse $response,
	): Application
	{
		return new Application(
			$presenterFactory,
			$router,
			$request,
			$response
		);
	}

}
