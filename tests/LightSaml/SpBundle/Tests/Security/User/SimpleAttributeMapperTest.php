<?php

namespace LightSaml\SpBundle\Tests\Security\User;

use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\Attribute;
use LightSaml\Model\Assertion\AttributeStatement;
use LightSaml\Model\Protocol\Response;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpResponseToken;
use LightSaml\SpBundle\Security\User\SimpleAttributeMapper;

class SimpleAttributeMapperTest extends \PHPUnit_Framework_TestCase
{
    public function test_get_attributes_from_single_assertion_response()
    {
        $assertion = $this->buildAssertion([
            'organization' => 'test',
            'name' => 'John',
            'email_address' => 'john@domain.com',
            'test' => ['one', 'two'],
        ]);
        $response = $this->buildResponse($assertion);
        $samlSpResponseToken = $this->buildSamlSpResponseToken($response);

        $expectedAttributes = [
            'organization' => 'test',
            'name' => 'John',
            'email_address' => 'john@domain.com',
            'test' => ['one', 'two'],
        ];

        $simpleAttributeMapper = new SimpleAttributeMapper();
        $actualAttributes = $simpleAttributeMapper->getAttributes($samlSpResponseToken);

        $this->assertEquals($expectedAttributes, $actualAttributes);
    }

    public function test_get_attributes_from_multi_assertions_response()
    {
        $assertion = $this->buildAssertion([
            'organization' => 'test',
            'name' => 'John',
            'email_address' => 'john@domain.com',
            'test' => ['one', 'two'],
        ]);
        $response = $this->buildResponse($assertion);

        $assertion = $this->buildAssertion([
            'name' => 'Doe',
            'email_address' => 'doe@domain.com',
            'test' => ['three', 'four'],
        ]);
        $response = $this->buildResponse($assertion, $response);

        $samlSpResponseToken = $this->buildSamlSpResponseToken($response);

        $expectedAttributes = [
            'organization' => 'test',
            'name' => ['John', 'Doe'],
            'email_address' => ['john@domain.com', 'doe@domain.com'],
            'test' => ['one', 'two', 'three', 'four'],
        ];

        $simpleAttributeMapper = new SimpleAttributeMapper();
        $actualAttributes = $simpleAttributeMapper->getAttributes($samlSpResponseToken);

        $this->assertEquals($expectedAttributes, $actualAttributes);
    }

    public function test_get_attributes_from_multi_attribute_statements_response()
    {
        $assertion = $this->buildAssertion([
            'organization' => 'test',
            'name' => 'John',
            'email_address' => 'john@domain.com',
            'test' => ['one', 'two']
        ]);
        $assertion = $this->buildAssertion([
            'name' => 'Doe',
            'email_address' => 'doe@domain.com',
            'test' => ['three', 'four']
        ], $assertion);
        $response = $this->buildResponse($assertion);

        $samlSpResponseToken = $this->buildSamlSpResponseToken($response);

        $expectedAttributes = [
            'organization' => 'test',
            'name' => ['John', 'Doe'],
            'email_address' => ['john@domain.com', 'doe@domain.com'],
            'test' => ['one', 'two', 'three', 'four'],
        ];

        $simpleAttributeMapper = new SimpleAttributeMapper();
        $actualAttributes = $simpleAttributeMapper->getAttributes($samlSpResponseToken);

        $this->assertEquals($expectedAttributes, $actualAttributes);
    }

    /**
     * @param Response $response
     *
     * @return \LightSaml\SpBundle\Security\Authentication\Token\SamlSpResponseToken
     */
    private function buildSamlSpResponseToken(Response $response)
    {
        return new SamlSpResponseToken($response, 'test');
    }

    /**
     * @param Assertion $assertion
     * @param Response $response
     *
     * @return Response
     */
    private function buildResponse(Assertion $assertion, Response $response = null)
    {
        if (null == $response) {
            $response = new Response();
        }

        $response->addAssertion($assertion);

        return $response;
    }

    /**
     * @param array $assertionAttributes
     * @param Assertion $assertion
     *
     * @return Assertion
     */
    private function buildAssertion(array $assertionAttributes, Assertion $assertion = null)
    {
        if (null == $assertion) {
            $assertion = new Assertion();
        }

        $assertion->addItem($attributeStatement = new AttributeStatement());
        foreach ($assertionAttributes as $attributeName => $attributeValue) {
            $attributeStatement->addAttribute(new Attribute($attributeName, $attributeValue));
        }

        return $assertion;
    }
}
