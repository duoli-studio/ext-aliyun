<?php

namespace Poppy\Framework\Extension\GraphQL\Queries;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\Extension\Extension;
use Poppy\Framework\GraphQL\Abstracts\Query;

/**
 * Class ConfigurationQuery.
 */
class ExtensionQuery extends Query
{
    /**
     * @param $root
     * @param $args
     *
     * @return array
     */
    public function resolve($root, $args)
    {
        return $this->extension->repository()->map(function (Extension $extension) {
            $authors = (array)$extension->get('authors');
            foreach ($authors as $key => $author) {
                $string = $author['name'] ?? '';
                $string .= ' <';
                $string .= $author['email'] ?? '';
                $string .= '>';
                $authors[$key] = $string;
            }
            $extension->offsetSet('authors', implode(',', $authors));
            $require = $extension->get('require');
            $extension->offsetSet('requireInstall', $require['install']);
            $extension->offsetSet('requireUninstall', $require['uninstall']);

            return $extension;
        })->toArray();
    }

    /**
     * @return \GraphQL\Type\Definition\ListOfType
     */
    public function type(): ListOfType
    {
        return Type::listOf($this->graphql->type('extension'));
    }
}
