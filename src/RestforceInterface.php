<?php
namespace EventFarm\Restforce;

use EventFarm\Restforce\Rest\OAuthAccessToken;
use Psr\Http\Message\ResponseInterface;

interface RestforceInterface
{
    public function userInfo(): ResponseInterface;
    public function limits(): ResponseInterface;
    public function query(string $soqlQuery): ResponseInterface;
    public function create(string $sobjectType, array $data): ResponseInterface;
    public function update(string $sobjectType, string $sobjectId, array $data): ResponseInterface;
    public function describe(string $sobjectType): ResponseInterface;
    public function find(string $sobjectType, string $sobjectId, array $fields = []): ResponseInterface;
    public function getNext(string $url): ResponseInterface;
    public function refreshToken(): ?OAuthAccessToken;
}
