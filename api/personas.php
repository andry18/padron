<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require 'conexion.php';

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]));
}

// Obtener la acción solicitada
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'getAll':
        getAllPersonas($conn);
        break;
    case 'getOne':
        getOnePersona($conn, $_GET['id']);
        break;
    case 'create':
        createPersona($conn, $_POST);
        break;
    case 'update':
        updatePersona($conn, $_POST);
        break;
    case 'delete':
        deletePersona($conn, $_GET['id']);
        break;
    case 'search':
        searchPersonas($conn, $_GET['term']);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}

function getAllPersonas($conn) {
    $sql = "SELECT * FROM personas ORDER BY paterno, materno, nombre";
    $result = $conn->query($sql);
    
    $personas = [];
    while ($row = $result->fetch_assoc()) {
        $personas[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $personas]);
}

function getOnePersona($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM personas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $persona = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $persona]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Persona no encontrada']);
    }
}

function createPersona($conn, $data) {
    $stmt = $conn->prepare("INSERT INTO personas (paterno, materno, nombre) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $data['paterno'], $data['materno'], $data['nombre']);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Persona creada correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear persona: ' . $stmt->error]);
    }
}

function updatePersona($conn, $data) {
    $stmt = $conn->prepare("UPDATE personas SET paterno = ?, materno = ?, nombre = ? WHERE id = ?");
    $stmt->bind_param("sssi", $data['paterno'], $data['materno'], $data['nombre'], $data['id']);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Persona actualizada correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar persona: ' . $stmt->error]);
    }
}

function deletePersona($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM personas WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Persona eliminada correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar persona: ' . $stmt->error]);
    }
}

function searchPersonas($conn, $term) {
    $term = "%$term%";
    $stmt = $conn->prepare("SELECT * FROM personas WHERE paterno LIKE ? OR materno LIKE ? OR nombre LIKE ? ORDER BY paterno, materno, nombre");
    $stmt->bind_param("sss", $term, $term, $term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $personas = [];
    while ($row = $result->fetch_assoc()) {
        $personas[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $personas]);
}

$conn->close();
?>