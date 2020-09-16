<?php

namespace Tighten\Concerns;

use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;

trait IdentifiesModelMethodTypes
{
    private static $relationshipMethods = [
        'hasOne',
        'belongsTo',
        'hasMany',
        'belongsToMany',
        'hasManyThrough',
        'morphTo',
        'morphMany',
        'morphToMany',
        'morphedByMany',
    ];

    private static $relationshipReturnTypes = [
        'Illuminate\Database\Eloquent\Relations\HasOne',
        'Illuminate\Database\Eloquent\Relations\BelongsTo',
        'Illuminate\Database\Eloquent\Relations\HasMany',
        'Illuminate\Database\Eloquent\Relations\BelongsToMany',
        'Illuminate\Database\Eloquent\Relations\HasManyThrough',
        'Illuminate\Database\Eloquent\Relations\MorphTo',
        'Illuminate\Database\Eloquent\Relations\MorphMany',
        'Illuminate\Database\Eloquent\Relations\MorphToMany',
        'Illuminate\Database\Eloquent\Relations\MorphedByMany',
    ];

    private function isScopeMethod(ClassMethod $stmt)
    {
        return strpos($stmt->name, 'scope') === 0;
    }

    private function isAccessorMethod(ClassMethod $stmt)
    {
        return strpos($stmt->name, 'get') === 0
            && strpos($stmt->name, 'Attribute') === strlen($stmt->name) - 9;
    }

    private function isMutatorMethod(ClassMethod $stmt)
    {
        return strpos($stmt->name, 'set') === 0
            && strpos($stmt->name, 'Attribute') === strlen($stmt->name) - 9;
    }

    private function isBootingMethod(ClassMethod $stmt)
    {
        return $stmt->isPublic()
            && $stmt->isStatic()
            && $stmt->name == 'booting';
    }

    private function isBootMethod(ClassMethod $stmt)
    {
        return $stmt->isPublic()
            && $stmt->isStatic()
            && $stmt->name == 'boot';
    }

    private function isBootedMethod(ClassMethod $stmt)
    {
        return $stmt->isPublic()
            && $stmt->isStatic()
            && $stmt->name == 'booted';
    }

    private function isCustomStaticMethod(ClassMethod $stmt)
    {
        return $stmt->isStatic();
    }

    private function isCustomMethod(ClassMethod $stmt)
    {
        if ($stmt->isAbstract()) {
            return true;
        }

        if (! $stmt->isPublic()) {
            return true;
        }

        if ($stmt->isStatic()) {
            return true;
        }

        return false;
    }

    private function isRelationshipMethod(ClassMethod $stmt)
    {
        if (! empty($stmt->getParams())) {
            return false;
        }

        if (in_array((string) $stmt->getReturnType(), self::$relationshipReturnTypes)) {
            return true;
        }

        if (empty($stmt->getStmts())) {
            return false;
        }

        $returnStmts = array_filter($stmt->getStmts(), function (Stmt $stmt) {
            return $stmt instanceof Return_;
        });
        $returnStmt = array_shift($returnStmts);

        if (is_null($returnStmt)) {
            return false;
        }

        if (
            $returnStmt->expr->var->name == 'this'
            && in_array($returnStmt->expr->name, self::$relationshipMethods)
        ) {
            return true;
        }

        return false;
    }
}
