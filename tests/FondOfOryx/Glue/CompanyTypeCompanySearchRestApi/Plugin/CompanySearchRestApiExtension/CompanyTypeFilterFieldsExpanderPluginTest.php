<?php

namespace FondOfOryx\Glue\CompanyTypeCompanySearchRestApi\Plugin\CompanySearchRestApiExtension;

use ArrayObject;
use Codeception\Test\Unit;
use FondOfOryx\Shared\CompanyTypeCompanySearchRestApi\CompanyTypeCompanySearchRestApiConstants;
use PHPUnit\Framework\MockObject\MockObject;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class CompanyTypeFilterFieldsExpanderPluginTest extends Unit
{
    /**
     * @var \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected RestRequestInterface|MockObject $restRequestMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\HttpFoundation\Request
     */
    protected MockObject|Request $httpRequestMock;

    /**
     * @var \ArrayObject<\PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\FilterFieldTransfer>
     */
    protected ArrayObject $filterFieldTransferMocks;

    /**
     * @var \FondOfOryx\Glue\CompanyTypeCompanySearchRestApi\Plugin\CompanySearchRestApiExtension\CompanyTypeFilterFieldsExpanderPlugin
     */
    protected CompanyTypeFilterFieldsExpanderPlugin $plugin;

    /**
     * @Override
     *
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->restRequestMock = $this->getMockBuilder(RestRequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->httpRequestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterFieldTransferMocks = new ArrayObject();

        $this->plugin = new CompanyTypeFilterFieldsExpanderPlugin();
    }

    /**
     * @return void
     */
    public function testExpand(): void
    {
        $companyType = 'manufacturer';

        $this->httpRequestMock->query = new ParameterBag(
            [
                CompanyTypeCompanySearchRestApiConstants::PARAMETER_NAME_TYPE => $companyType,
            ],
        );

        $this->restRequestMock->expects(static::atLeastOnce())
            ->method('getHttpRequest')
            ->willReturn($this->httpRequestMock);

        $filterFieldTransfers = $this->plugin->expand($this->restRequestMock, $this->filterFieldTransferMocks);

        static::assertCount(1, $filterFieldTransfers);

        $filterFieldTransfer = $filterFieldTransfers->offsetGet(0);

        static::assertEquals($companyType, $filterFieldTransfer->getValue());
        static::assertEquals(
            CompanyTypeCompanySearchRestApiConstants::FILTER_FIELD_TYPE_TYPE,
            $filterFieldTransfer->getType(),
        );
    }

    /**
     * @return void
     */
    public function testExpandWithEmptyParameterBag(): void
    {
        $this->httpRequestMock->query = new ParameterBag();

        $this->restRequestMock->expects(static::atLeastOnce())
            ->method('getHttpRequest')
            ->willReturn($this->httpRequestMock);

        $filterFieldTransfers = $this->plugin->expand($this->restRequestMock, $this->filterFieldTransferMocks);

        static::assertCount(0, $filterFieldTransfers);
    }
}
