<?php

namespace App\Form\Filters;

use App\Model\Enum\FilterTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class AbstractFilterType extends AbstractType
{
    protected abstract function buildFilterForm(FormBuilderInterface $builder): void;

    /**
     * @return array<string, FilterTypeEnum>
     */
    public static abstract function defineFilterTypes(): array;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->buildFilterForm($builder);

        $builder->add('save', SubmitType::class);
    }

}