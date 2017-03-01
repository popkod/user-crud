<?php

namespace PopCode\UserCrud\Interfaces;

interface UserControllerInterface
{
    public function __construct();

    public function index();

    public function create();

    public function store();

    public function show($id);

    public function edit($id);

    public function update($id);

    public function destroy($id);
}
