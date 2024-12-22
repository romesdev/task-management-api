<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Task Management API",
 *     version="1.0.0",
 *     description="API para gerenciar tarefas."
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
abstract class Controller
{
    //
}
