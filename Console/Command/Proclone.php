<?php
/**
 * Copyright © Encora Digital LLC All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Encora\ProductClone\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\Product\Gallery\Processor;


class Proclone extends Command
{

    const SKU_ARGUMENT = "sku";
    const BATCH_OPTION = "batch";
    const LOT_OPTION = "lot";

    protected $cloneTypes = array('used', 'refurbished');

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $product;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    private $configurable;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    private $collection;


    public function __construct(
        State $state,
        ProductFactory $product,
        collection $collection,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
    ) {
        $this->state = $state;
        $this->product = $product;
        $this->collection = $collection;
        $this->configurable = $configurable;
        parent::__construct();

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/clone.log');
        $this->logger = new \Zend_Log();
        $this->logger->addWriter($writer);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        $sku = $input->getArgument(self::SKU_ARGUMENT);
        $lot = $input->getOption(self::LOT_OPTION);
        $batch = $input->getOption(self::BATCH_OPTION);

        if(isset($sku) && $sku != null) {

            $product = $this->product->create()->loadByAttribute('sku',$sku);
            
            $parents = $this->configurable->getParentIdsByChild($product->getId());

            if ($product->getTypeId() != 'simple'){
                $output->writeln('<error>Not a Simple Product </error> '. $sku);
                $this->logger->err('Not a Simple product '. $sku);
                return 0;
            } elseif(!empty($parents)){
                $output->writeln('<error>Can not copy configurable product child </error> '. $sku);
                $this->logger->err('Can not copy configurable product child  '. $sku);
                return 0;
            } else {
                /** Process Message */
                $output->writeln('<info>'.'Replicating SKU '.$sku.'...</info>');

                foreach($this->cloneTypes as $type){
                    $this->createReplica($product,$output,$type);
                }
            }
        } elseif($lot != null && isset($lot)) {

            $productCollection = $this->collection;
            $productCollection->addAttributeToFilter('type_id', 'simple');
            $productCollection->addAttributeToSelect('*');
            $productCollection->setPageSize($lot);

            if(!empty($batch) && $batch != null ) {
                $productCollection->setCurPage($batch);
            }
            
            /** Process Message */
            $output->writeln('<info>'.'Replicating</info> '. $lot .'<info> products of batch number </info>'.$batch.'<info> Total Products </info>'.$productCollection->getSize());

            foreach($productCollection as $product){
                foreach($this->cloneTypes as $type){
                    $this->createReplica($product,$output,$type);
                }
            }

        } else {
            $productCollection = $this->collection;
            $productCollection->addAttributeToFilter('type_id', 'simple');
            $productCollection->addAttributeToSelect('*');
            
            /** Process Message */
            $output->writeln('<info>Replicating</info> all <info>products<info>');

            foreach($productCollection as $product){
                foreach($this->cloneTypes as $type){
                    $this->createReplica($product,$output,$type);
                }
            }
            
        }
        return 1;
    }

    protected function createReplica($product,$output,$type) {
        try{
            $cloneProduct = $this->product->create();
            $cloneProduct->setId(null);
            $cloneProduct->setSku($product->getSku().'_'.$type);
            $cloneProduct->setName($product->getName().'_'.$type);
            $cloneProduct->setAttributeSetId($product->getAttributeSetId());
            $cloneProduct->setStatus(1);
            $cloneProduct->setWeight($product->getWeight());
            $cloneProduct->setVisibility(4);
            $cloneProduct->setTaxClassId(0);
            $cloneProduct->setTypeId($product->getTypeId());
            $cloneProduct->setPrice($product->getPrice());
            $cloneProduct->setData('condition', $type);
            $cloneProduct->setStockData(
                array(
                    'use_config_manage_stock' => 0,
                    'manage_stock' => 1,
                    'is_in_stock' => 1,
                    'qty' => 9
                )
            );

            $data = $cloneProduct->save();
            
            $output->writeln('<info>Product </info>'.$data->getName().'<info> cloned, with Condition </info>'.$type);

            $this->logger->info('Product Sku'.$data->getSku().' Replicated');
        } catch (\Exception $e) {
            $output->writeln('<error>'.'Product Cloning Error: '.$e->getMessage().'</error>');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("encora:product:clone");
        $this->setDescription("Clone Simple Products");
        $this->setDefinition([
            new InputArgument(self::SKU_ARGUMENT, InputArgument::OPTIONAL, "Option to add Sku")
        ]);

        $this->addOption(
                self::BATCH_OPTION, 
                null, 
                InputOption::VALUE_REQUIRED, 
                "Option to add Batch");

        $this->addOption(
                self::LOT_OPTION,
                null,
                InputOption::VALUE_REQUIRED,
                "Option to add Lot"
            );

        parent::configure();
    }
}
