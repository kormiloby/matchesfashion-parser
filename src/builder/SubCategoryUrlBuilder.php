<?php
namespace Matchesfashion\Builder;

class SubCategoryUrlBuilder extends CategoryUrlBuilder
{
    protected $selecter = '.filter__box__category .innerFilterMobile a';
}
