public function update(Request $request, $id)
{
    $request->merge([
        'rut' => RutHelper::normalizar($request->rut),
    ]);

    // ID recibido: corresponde a admin_urgencias.id
    $adminUrgencia = AdminUrgencia::findOrFail($id);

    // Obtener el usuario mediante la relación real
    $usuario = User::findOrFail($adminUrgencia->user_id);

    $request->validate([
        'nombre' => 'required|max:50',
        'apellido' => 'required|max:50',
        'rut' => [
            'required',
            'max:10',
            'unique:admin_urgencias,rut,' . $adminUrgencia->id,
            'unique:users,rut,' . $usuario->id,
            new RutChileno,
        ],
        'password' => 'nullable|min:8|confirmed',
    ]);

    $adminUrgencia->nombre = strtoupper($request->nombre);
    $adminUrgencia->apellido = strtoupper($request->apellido);
    $adminUrgencia->rut = $request->rut;
    $adminUrgencia->save();

    $usuario->name = strtoupper($request->nombre);
    $usuario->apellido = strtoupper($request->apellido);
    $usuario->rut = $request->rut;

    if ($request->filled('password')) {
        $usuario->password = Hash::make($request->password);
    }

    $usuario->save();

    return redirect()
        ->route('admin.admin_urgencias.index')
        ->with('mensaje', 'Usuario actualizado correctamente.')
        ->with('icono', 'success');
}
