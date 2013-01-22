<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Pricing\Entity;

use Vespolina\Entity\Pricing\PricingSetInterface;

class PricingSet implements PricingSetInterface
{
    protected $context;
    protected $elements;
    protected $processed;
    protected $processingState = self::PROCESSING_UNPROCESSED;
    protected $returns;

    const PROCESSING_UNPROCESSED = 0;
    const PROCESSING_FINISHED = 1;

    public function __construct(array $customReturns = array())
    {
        $defaultReturns = array(
            'discounts', 'netValue', 'surcharge', 'taxes', 'totalValue'
        );
        $this->returns = array_merge($defaultReturns, $customReturns);

        foreach ($this->returns as $return) {
            $this->processed[$return] = null;
        }
    }

    public function getDiscounts()
    {
        return $this->get('discounts');
    }

    public function getNetValue()
    {
        return $this->get('netValue');
    }

    public function getSurcharge()
    {
        return $this->get('surcharge');
    }

    public function getTaxes()
    {
        return $this->get('taxes');
    }

    public function getTotalValue()
    {
        return $this->get('totalValue');
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function get($name)
    {
        if ($this->processingState != self::PROCESSING_FINISHED) {
            throw new \Exception();
        }

        return $this->processed[$name];
    }

    public function process($context = null)
    {
        // create empty array with keys from $this->processed
        $processed = array();
        foreach ($this->elements as $element) {
            $processed = array_merge($this->processed, $element->process($context, $processed));
        }

        $this->processed = $processed;
        $this->processingState = self::PROCESSING_FINISHED;
    }
}