<?php
session_start();
require_once './connect.php';
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset=utf-8>
		<title>Three.js app</title>
		<style>
		body { margin: 0; }
		canvas { width: 100%; height: 100% }
		</style>
		<link rel="stylesheet" href="./css/style.css">
		<script src="./js/script.js"></script>
		<script src="./js/jquery-3.7.0.min.js"></script>
		
		<?php require "./create_admin.php"?>
    </head>

    <body>
		
		<input type="checkbox" id="nav-toggle" hidden>

		<nav class="nav">

		<?php require_once './reg_auth.php' ?>


			<label for="nav-toggle" class="nav-toggle" onclick></label>
			 
			<h2 class="logo"> 
				<a href="">menu</a> 
			</h2>
			<ul>
				<li> 
					
					<!-- Rounded switch -->
					<label class="switch">
					  <input type="checkbox" id="move_or_edit">
					  <span class="slider round"></span>
					  
					</label> 
					режим перемещения/редактирования
				<li> <!-- Rounded switch -->
					<label class="switch">
					  <input type="checkbox" id="add_or_remove">
					  <span class="slider round"></span>
					</label> добавить/удалить

				
				
				
			</ul>
		</nav>
		

		<script type="module">
			import * as THREE from './build/three.module.js';

			import {OrbitControls} from './js/jsm/controls/OrbitControls.js';
			import {DragControls} from './js/DragControls.js';

			import { FontLoader } from './js/jsm/loaders/FontLoader.js';
			import { CSS2DObject, CSS2DRenderer } from './js/jsm/renderers/CSS2DRenderer.js';

			


			

			let camera, drag_controls, orbit_controls, container, scene, renderer, labelRenderer;

			let plane;
			let pointer, raycaster, isShiftDown = false;

			let rollOverMesh, rollOverMaterial;
			let cubeGeo, cubeMaterial;

			let enableSelection;


			const objects = [];
			const cubes = [];
			const snap_zones = [];
			const beacons = [];
			
			const mouse = new THREE.Vector2();

			let checkbox_control =  document.getElementById("move_or_edit");
			let checkbox_add_or_remove = document.getElementById("add_or_remove");
			let checkbox_on_off_label = document.getElementById("on_off_label");  
			
			

			init();
			animate();

			
			

			function init() {
				checkbox_control.checked = true;

				container = document.getElementById( 'container' );
				//scene
				
				scene = new THREE.Scene();
				scene.background = new THREE.Color( 0x000 );

				

				//camera

				camera = new THREE.PerspectiveCamera( 60, window.innerWidth / window.innerHeight, 1, 1000 );
				camera.position.set( 10, 20, 1 );
				camera.lookAt(0, 0, 0);
				


				// lights

				let spotLight = new THREE.SpotLight(0xffffff);
				spotLight.position.set(100, 100, 100);
				scene.add(spotLight);	

				let spotLight2 = new THREE.SpotLight(0xffffff);
				spotLight2.position.set( -200, -200, -200);
				scene.add(spotLight2);
				

				//renderer

				renderer = new THREE.WebGLRenderer( { antialias: true } );
				renderer.setPixelRatio( window.devicePixelRatio );
				renderer.setSize( window.innerWidth, window.innerHeight );
				document.body.appendChild( renderer.domElement );
			
				labelRenderer = new CSS2DRenderer();
				labelRenderer.setSize( window.innerWidth, window.innerHeight );
				labelRenderer.domElement.style.position = 'absolute';
				labelRenderer.domElement.style.top = '0px';
				labelRenderer.domElement.style.pointerEvents = 'none'
				document.body.appendChild( labelRenderer.domElement );

				

				

				window.addEventListener( 'resize', onWindowResize );
				document.addEventListener( 'pointermove', onPointerMove );
				document.addEventListener( 'pointerdown', onPointerDown );
				
				
				
				//controls

				drag_controls = new DragControls( cubes, camera, renderer.domElement );				

				

				orbit_controls = new OrbitControls( camera, renderer.domElement );

			
				//axes

				const axesHelper = new THREE.AxesHelper( 100 );
				axesHelper.position.y = 0.25;
				scene.add( axesHelper );
	
				
				

				// grid

				const gridHelper1 = new THREE.GridHelper( 60, 60, 0xffffff, 0xffffff);
				gridHelper1.position.y = 0.25;
				scene.add(  gridHelper1 );

				//geometries
							
				const geometry_box_horizontal = new THREE.BoxGeometry(60,0.5,40 );
				const geometry_box_vertical = new THREE.BoxGeometry( 10, 0.2, 40 ); 
				const geometry_box_vertical2 = new THREE.BoxGeometry(60, 0.2, 10 );
				const geometry_box_vertical_inside_1 = new THREE.BoxGeometry(10, 0.2, 18 );
				const geometry_box_vertical_inside_2 = new THREE.BoxGeometry(10, 0.2, 25 );
				

				//materials

				const loader_texture = new THREE.TextureLoader();	
				const loader = new THREE.TextureLoader();

				const capsule_material = new THREE.MeshPhongMaterial({
					color: 0x000000,
					side: THREE.DoubleSide,
				});

				cubeMaterial = new THREE.MeshPhongMaterial({
					color: 0xff0000
				});
				
				const material_floor = [
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/texture_floor.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				];
				
				const material_walls_outside_right = [
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/texture_inside.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				];

				const material_walls_outside_left = [
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/texture_inside.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				];
				 
				const material_walls_inside = [
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/texture_inside.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/texture_inside.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				];

				const material_back_wall = [
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/texture_inside.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/texture_outside.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				new THREE.MeshPhongMaterial( {
				map: loader_texture.load('./sources/textures/concrete.jpg'),
				side: THREE.DoubleSide
				} ),
				];

				const material_beacon =  new THREE.MeshPhongMaterial({color: 0xff0000});
				const material_sphere =  new THREE.MeshBasicMaterial({color: 0xfafafa, transparent: true, opacity: 0.5});
				//mesh add

				function add_object(geometry, material, angle_x, angle_y, angle_z, pos_x, pos_y, pos_z) {
					const mesh_object = new THREE.Mesh( geometry, material);
					var angle_x_rad = Math.PI / 180 * angle_x;
					var angle_y_rad = Math.PI / 180 * angle_y;
					var angle_z_rad = Math.PI / 180 * angle_z;
					mesh_object.rotation.set(angle_x_rad, angle_y_rad, angle_z_rad);
					mesh_object.position.set(pos_x, pos_y, pos_z);
					scene.add(mesh_object);

					return mesh_object;
				}



				//beacons add

				const beacon_geometry =new THREE.SphereGeometry(0.2, 32, 32, 0, Math.PI);
				const box_geometry =new THREE.BoxGeometry(1, 0.1, 1);

				
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 0, 0, 28, 8, 2.1));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 0, 0, 18, 5, 2.1));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 0, 0, 8, 8, 2.1));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 180, 0, 25, 8, 19.9));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 180, 0, 18, 5, 19.9));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 180, 0, 5, 8, 19.9));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 90, 0, 0.1, 5, 12));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, -90, 0, 29.9, 8, 10));
				add_object(box_geometry, material_walls_outside_right, 0, 0, 0, 15, 10, 10);
				beacons.push(add_object(beacon_geometry, material_beacon, 90, 0, 0, 15, 10, 10));
				add_object(box_geometry, material_walls_outside_right, 0, 0, 0, 25, 10, 10);
				beacons.push(add_object(beacon_geometry, material_beacon, 90, 0, 0, 25, 10, 10));
				beacons.push(add_object(box_geometry, material_walls_outside_right, 0, 0, 0, 5, 10, 10));
				beacons.push(add_object(beacon_geometry, material_beacon, 90, 0, 0, 5, 10, 10));


				beacons.push(add_object(beacon_geometry, material_beacon, 0, 180, 0, 28, 5, -2.1));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 180, 0, 18, 8, -2.1));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 180, 0, 8, 5, -2.1));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 0, 0, 25, 8, -19.9));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 0, 0, 15, 5, -19.9));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 0, 0, 5, 8, -19.9));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 90, 0, 0.1, 5, -10));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, -90, 0, 29.9, 8, -10));
				add_object(box_geometry, material_walls_outside_right, 0, 0, 0, 15, 10, -10);
				beacons.push(add_object(beacon_geometry, material_beacon, 90, 0, 0, 15, 10, -10));
				add_object(box_geometry, material_walls_outside_right, 0, 0, 0, 25, 10, -10);
				beacons.push(add_object(beacon_geometry, material_beacon, 90, 0, 0, 25, 10, -10));
				add_object(box_geometry, material_walls_outside_right, 0, 0, 0, 5, 10, -10);
				beacons.push(add_object(beacon_geometry, material_beacon, 90, 0, 0, 5, 10, -10));



				beacons.push(add_object(beacon_geometry, material_beacon, 0, 180, 0, -8, 8, -2.1));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 180, 0, -12, 5, -2.1));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 0, 0, -5, 8, -19.9));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 0, 0, -12, 5, -19.9));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 90, 0, -20, 8, 10));
				add_object(box_geometry, material_walls_outside_right, 0, 0, 0, -5, 10, -10);
				beacons.push(add_object(beacon_geometry, material_beacon, 90, 0, 0, -5, 10, -10));
				add_object(box_geometry, material_walls_outside_right, 0, 0, 0, -15, 10, -10);
				beacons.push(add_object(beacon_geometry, material_beacon, 90, 0, 0, -15, 10, -10));
				

				beacons.push(add_object(beacon_geometry, material_beacon, 0, 0, 0, -8, 8, 2.1));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 0, 0, -18, 5, 2.1));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 180, 0, -5, 8, 19.9));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 180, 0, -15, 8, 19.9));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 90, 0, -20, 8, -12));
				add_object(box_geometry, material_walls_outside_right, 0, 0, 0, -5, 10, 10);
				beacons.push(add_object(beacon_geometry, material_beacon, 90, 0, 0, -5, 10, 10));
				add_object(box_geometry, material_walls_outside_right, 0, 0, 0, -10, 10, 10);
				beacons.push(add_object(beacon_geometry, material_beacon, 90, 0, 0, -10, 10, 10));

				beacons.push(add_object(beacon_geometry, material_beacon, 0, 90, 0, -23, 5, 2.1));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 180, 0, -29.9, 8, 19.9));
				add_object(box_geometry, material_walls_outside_right, 0, 0, 0, -25, 10, 10);
				beacons.push(add_object(beacon_geometry, material_beacon, 90, 0, 0, -25, 10, 10));

				beacons.push(add_object(beacon_geometry, material_beacon, 0, 90, 0, -23, 5, -2.1));
				beacons.push(add_object(beacon_geometry, material_beacon, 0, 0, 0, -29.9, 8, -19.9));
				add_object(box_geometry, material_walls_outside_right, 0, 0, 0, -25, 10, -10);
				beacons.push(add_object(beacon_geometry, material_beacon, 90, 0, 0, -25, 10, -10));

				


				//sphere add
				

				const sphere_geometry =new THREE.SphereGeometry(10  , 32, 32);

				//side walls
				const geometry_box_outside_walls_up_1 = new THREE.BoxGeometry(5, 0.2, 40 );
				const geometry_box_outside_walls_down_11 = new THREE.BoxGeometry(5, 0.2, 6 );
				const geometry_box_outside_walls_down_12 = new THREE.BoxGeometry(5, 0.2, 16 );
				const geometry_box_outside_walls_down_13 = new THREE.BoxGeometry(5, 0.2, 28 );

				add_object(geometry_box_outside_walls_up_1,  material_walls_outside_right, 0, 0, 90, 30, 7.5, 0 );
				add_object(geometry_box_outside_walls_down_11,  material_walls_outside_right, 0, 0, 90, 30, 2.5, 17 );
				add_object(geometry_box_outside_walls_down_12,  material_walls_outside_right, 0, 0, 90, 30, 2.5, 0 );
				add_object(geometry_box_outside_walls_down_11,  material_walls_outside_right, 0, 0, 90, 30, 2.5, -17 );

				add_object(geometry_box_outside_walls_up_1,  material_walls_outside_right, 0, 0, 90, -30, 7.5, 0 );
				add_object(geometry_box_outside_walls_down_11,  material_walls_outside_right, 0, 0, 90, -30, 2.5, 17 );
				add_object(geometry_box_outside_walls_down_13,  material_walls_outside_right, 0, 0, 90, -30, 2.5, -6 );
				
				//floor
				plane = add_object(geometry_box_horizontal, material_floor, 0, 0, 0, 0, 0, 0 );
				objects.push(plane);
				snap_zones.push(plane);


				//front and back walls

				const geometry_box_outside_walls_up_2 = new THREE.BoxGeometry(60, 0.2, 5);
				const geometry_box_outside_walls_down_21 = new THREE.BoxGeometry(12, 0.2, 5 );
				const geometry_box_outside_walls_down_22 = new THREE.BoxGeometry(19, 0.2, 5 );
				const geometry_box_outside_walls_down_23 = new THREE.BoxGeometry(16, 0.2, 5 );
				const geometry_box_outside_walls_down_24 = new THREE.BoxGeometry(1, 0.2, 3 );
				const geometry_box_outside_walls_down_25 = new THREE.BoxGeometry(17, 0.2, 5 );

				add_object(geometry_box_outside_walls_up_2,  material_back_wall, 90, 0, 0, 0, 7.5, -20 );
				add_object(geometry_box_outside_walls_down_21,  material_back_wall, 90, 0, 0, 24, 2.5, -20 );
				add_object(geometry_box_outside_walls_down_22,  material_back_wall, 90, 0, 0, 2.5, 2.5, -20 );
				add_object(geometry_box_outside_walls_down_23,  material_back_wall, 90, 0, 0, -21, 2.5, -20 );
				add_object(geometry_box_outside_walls_down_24,  material_back_wall, 90, 0, 0, -29.5, 3.5, -20 );

				add_object(geometry_box_outside_walls_up_2,  material_back_wall, 90, 0, 0, 0, 7.5, 20 );
				add_object(geometry_box_outside_walls_down_21,  material_back_wall, 90, 0, 0, 24, 2.5, 20 );
				add_object(geometry_box_outside_walls_down_22,  material_back_wall, 90, 0, 0, 2.5, 2.5, 20 );
				add_object(geometry_box_outside_walls_down_25,  material_back_wall, 90, 0, 0, -21.5, 2.5, 20 );
		

				//inside walls geometry

				const geometry_box_inside_walls_up_3 = new THREE.BoxGeometry(5, 0.2, 40);
				const geometry_box_inside_walls_down_31 = new THREE.BoxGeometry(5, 0.2, 18);
				const geometry_box_inside_walls_up_4 = new THREE.BoxGeometry(5, 0.2, 60);
				const geometry_box_inside_walls_down_41 = new THREE.BoxGeometry(5, 0.2, 26);
				const geometry_box_inside_walls_down_42 = new THREE.BoxGeometry(5, 0.2, 16);
				const geometry_box_inside_walls_down_43 = new THREE.BoxGeometry(5, 0.2, 6);
				const geometry_box_inside_walls_down_44 = new THREE.BoxGeometry(5, 0.2, 8);
				const geometry_box_inside_walls_down_45 = new THREE.BoxGeometry(3, 0.2, 2);

				//office rooms geometry
				const geometry_box_inside_walls_up_51 = new THREE.BoxGeometry(8, 0.2, 18);
				const geometry_box_inside_walls_down_51 = new THREE.BoxGeometry(2, 0.2, 6);
				const geometry_box_inside_walls_down_52 = new THREE.BoxGeometry(2, 0.2, 3);
				const geometry_box_inside_walls_up_52 = new THREE.BoxGeometry(8, 0.2, 10);
				



				add_object(geometry_box_inside_walls_up_3,  material_back_wall, 0, 0, 90, -20, 7.5, 0 );
				add_object(geometry_box_inside_walls_down_31,  material_back_wall, 0, 0, 90, -20, 2.5, 11 );
				add_object(geometry_box_inside_walls_down_31,  material_back_wall, 0, 0, 90, -20, 2.5, -11 );

				add_object(geometry_box_inside_walls_up_3,  material_back_wall, 0, 0, 90, 0, 7.5, 0 );
				add_object(geometry_box_inside_walls_down_31,  material_back_wall, 0, 0, 90, 0, 2.5, 11 );
				add_object(geometry_box_inside_walls_down_31,  material_back_wall, 0, 0, 90, 0, 2.5, -11 );

				add_object(geometry_box_inside_walls_up_4,  material_back_wall, 90, 90, 0, 0, 7.5, 2 );
				add_object(geometry_box_inside_walls_down_41,  material_back_wall, 90, 90, 0, 17, 2.5, 2 );

				add_object(geometry_box_inside_walls_up_4,  material_back_wall, 90, 90, 0, 0, 7.5, -2 );
				add_object(geometry_box_inside_walls_down_41,  material_back_wall, 90, 90, 0, 17, 2.5, -2 );

				add_object(geometry_box_inside_walls_down_42,  material_back_wall, 90, 90, 0, -12, 2.5, -2 );
				add_object(geometry_box_inside_walls_down_42,  material_back_wall, 90, 90, 0, -12, 2.5, 2 );

				add_object(geometry_box_inside_walls_down_43,  material_back_wall, 90, 90, 0, -27, 2.5, 2 );

				add_object(geometry_box_inside_walls_down_44,  material_back_wall, 90, 90, 0, -24, 2.5, -2 );

				add_object(geometry_box_inside_walls_down_45,  material_back_wall, 90, 90, 0, -29, 3.5, -2 );

				add_object(geometry_box_inside_walls_up_51,  material_back_wall, 0, 0, 90, -28, 6, -11 );
				add_object(geometry_box_inside_walls_up_52,  material_back_wall, 90, 0, 0, -24, 5, -13);
				add_object(geometry_box_inside_walls_up_52,  material_back_wall, 90, 0, 0, -24, 5, -9);

				add_object(geometry_box_inside_walls_down_51,  material_back_wall, 0, 0, 90, -28, 1, -16);
				add_object(geometry_box_inside_walls_down_51,  material_back_wall, 0, 0, 90, -28, 1, -5);
				add_object(geometry_box_inside_walls_down_52,  material_back_wall, 0, 0, 90, -28, 1, -10.5);

				// racks
				const geometry_rack = new THREE.BoxGeometry(5, 0.1, 2);
				

				function create_rack(pos_x, pos_z) {

					const geometry_vertical = new THREE.BoxGeometry(0.2, 10, 0.2);
					const geometry_horizontal = new THREE.BoxGeometry(5, 0.2, 2);

					 add_object(geometry_vertical,  material_back_wall, 0, 0, 0, pos_x-2.5, 5, pos_z-1);
					 add_object(geometry_vertical,  material_back_wall, 0, 0, 0, pos_x-2.5, 5, pos_z+1);
					 add_object(geometry_vertical,  material_back_wall, 0, 0, 0, pos_x+2.5, 5, pos_z-1);
					 add_object(geometry_vertical,  material_back_wall, 0, 0, 0, pos_x+2.5, 5, pos_z+1);

					 const horizontal_1 = add_object(geometry_horizontal,  material_back_wall, 0, 0, 0,pos_x, 3, pos_z);
					 horizontal_1.name = "horizontal";
					 objects.push(horizontal_1);
					 snap_zones.push(horizontal_1);
					 const horizontal_2 = add_object(geometry_horizontal,  material_back_wall, 0, 0, 0, pos_x, 6, pos_z);
					 horizontal_2.name = "horizontal";
					 objects.push(horizontal_2);
					 snap_zones.push(horizontal_2);
					 const horizontal_3 = add_object(geometry_horizontal,  material_back_wall, 0, 0, 0, pos_x, 9, pos_z);
					 horizontal_3.name = "horizontal";
					 objects.push(horizontal_3);
					 snap_zones.push(horizontal_3);
				

				}

				create_rack(23, -7);
				create_rack(18, -7);
				create_rack(13, -7);
				create_rack(8, -7);

				create_rack(23, -15);
				create_rack(18, -15);
				create_rack(13, -15);
				create_rack(8, -15);

				create_rack(23, 7);
				create_rack(18, 7);
				create_rack(13, 7);
				create_rack(8, 7);

				create_rack(23, 15);
				create_rack(18, 15);
				create_rack(13, 15);
				create_rack(8, 15);

				create_rack(-7, 15);
				create_rack(-12, 15);
				
				create_rack(-7, 7);
				create_rack(-12, 7);

				create_rack(-7, -15);
				create_rack(-12, -15);
				
				create_rack(-7, -7);
				create_rack(-12, -7);

				create_rack(-23, 7);

				create_rack(-23, 15);
				
				
			
				// roll-over helpers

				const rollOverGeo = new THREE.BoxGeometry( 1, 1, 1 );
				rollOverMaterial = new THREE.MeshBasicMaterial( { color: 0xff0000, opacity: 0.5, transparent: true } );
				rollOverMesh = new THREE.Mesh( rollOverGeo, rollOverMaterial );
				scene.add( rollOverMesh );
				cubes.push(rollOverMesh);

				// cubes

				
				cubeGeo = new THREE.BoxGeometry( 1, 1, 1 );
				

				//

				raycaster = new THREE.Raycaster();
				pointer = new THREE.Vector2();


				//
				let result;

				function get_data() {
					return new Promise(function(resolve){
						$.ajax({
						url: './get_objects.php', 
						type: 'GET',
						success: function(response) {
							if (response.success) {
								//count_name = response.data[1];
							}
							resolve(response);
						}
						});
					});
				}

				get_data()
				.then(function(response) {

					console.log(response.data);
					
					let element;
					let label;
					let label2;
					


					for (let item of response.data) {
						
						const voxel = new THREE.Mesh( cubeGeo, cubeMaterial);
						label = new CSS2DObject(element);
						label2 = new CSS2DObject(element);
						label.element = document.createElement('div');
						label2.element = document.createElement('div');

						

						voxel.position.copy(JSON.parse(item.real_current_location));
						let current_location = JSON.parse(item.current_location);

							
									
							objects.push(voxel);
							cubes.push(voxel);

										
							label.element.innerHTML = 'x: ' + String(voxel.position.x.toFixed(3)) + '<br/>' + 'y: ' + String(voxel.position.y.toFixed(3)) + '<br/>' + 'z: ' + String(voxel.position.z.toFixed(3));
		
							label.position.z = label.position.z + 2;

							label2.element.innerHTML = 'x: ' + String(current_location.x.toFixed(3)) + '<br/>' + 'y: ' + String(current_location.y.toFixed(3)) + '<br/>' + 'z: ' + String(current_location.z.toFixed(3));
		
							label2.position.z = label2.position.z - 2;

							voxel.name = item.object_id;
							label.element.classList.add('label', item.object_id);
							label2.element.classList.add('label', item.object_id);

							scene.add(voxel);
							voxel.add(label);
							voxel.add(label2);

						
					}
					
				
				
				});
				

			}


			//
			
			function findClosestTarget(position, targets) {
				let closestTarget = null;
				let closestDistance = Infinity;

				for (let i = 0; i < targets.length; i++) {
					const target = targets[i];
			
					const distance = position.distanceTo(target.position);

					if (distance < closestDistance) {
					closestTarget = target;
					closestDistance = distance;
					}
				}

				return closestTarget;
				}
				
				

			//
			
			
			drag_controls.addEventListener('drag', function (event) {
				const object = event.object;
				const position = object.position.clone();
				const closestTarget = findClosestTarget(position, snap_zones); // Находим ближайший целевой объект
				if (closestTarget) {
					object.position.y = closestTarget.position.y;
					object.position.add(new THREE.Vector3(0, 0.75, 0 ))
				}

				let distance;
				let count = 0;
				let beacons_pos = [];
				let distances = [];
				for (let item of beacons) {
					distance = calculateDistance(item.position.x, item.position.y, item.position.z, event.object.position.x, event.object.position.y, event.object.position.z);
					if (distance < 15) {
						
						
						if (count == 3) break;
						
							beacons_pos.push(item.position);
							distances.push(distance);
						
						
						count++;
						
						
					};
					
					
					
				}
				for (let i = 0; i< beacons_pos.length; i++) {
					beacons_pos[i].r = distances[i];
					
				}

				let res_trilaterate = trilaterate(beacons_pos[0], beacons_pos[1], beacons_pos[2], true);
				let labels = document.getElementsByClassName(object.name);
				labels.item(0).innerHTML = 'x: ' + object.position.x.toFixed(3) + '<br/>' + 'y: ' + object.position.y.toFixed(3) + '<br/>' + 'z: ' + object.position.z.toFixed(3);
				labels.item(1).innerHTML = 'x: ' + res_trilaterate.x.toFixed(3) + '<br/>' + 'y: ' + res_trilaterate.y.toFixed(3) + '<br/>' + 'z: ' + res_trilaterate.z.toFixed(3);
			});


			drag_controls.addEventListener('dragend', function (event) {
				const object = event.object;
				const position = object.position.clone();
				const closestTarget = findClosestTarget(position, snap_zones); // Находим ближайший целевой объект
				if (closestTarget) {
					object.position.y = closestTarget.position.y;
					object.position.add(new THREE.Vector3(0, 0.75, 0 ))
				}

				let distance;
				let count = 0;
				let beacons_pos = [];
				let distances = [];
				for (let item of beacons) {
					distance = calculateDistance(item.position.x, item.position.y, item.position.z, event.object.position.x, event.object.position.y, event.object.position.z);
					if (distance < 15) {
						
						
						if (count == 3) break;
						
							beacons_pos.push(item.position);
							distances.push(distance);
						
						
						count++;
						
						
					};
					
					
					
				}
				for (let i = 0; i< beacons_pos.length; i++) {
					beacons_pos[i].r = distances[i];
					
				}

				let res_trilaterate = trilaterate(beacons_pos[0], beacons_pos[1], beacons_pos[2], true);
				let labels = document.getElementsByClassName(object.name);
				labels.item(0).innerHTML = 'x: ' + object.position.x.toFixed(3) + '<br/>' + 'y: ' + object.position.y.toFixed(3) + '<br/>' + 'z: ' + object.position.z.toFixed(3);
				labels.item(1).innerHTML = 'x: ' + res_trilaterate.x.toFixed(3) + '<br/>' + 'y: ' + res_trilaterate.y.toFixed(3) + '<br/>' + 'z: ' + res_trilaterate.z.toFixed(3);

				let now = new Date();			
				let formattedTime = now.toLocaleString('ru-RU', { timeZone: 'Europe/Moscow' });

				// Выполняем AJAX-запрос
				// Отправляем данные через AJAX
				function make_request() {
					return new Promise(function(resolve){
						$.ajax({
						url: './change_on_drag.php',  
						method: 'POST',
						data: {
							object_id: object.name,
							real_current_location: JSON.stringify(object.position),
							current_location: JSON.stringify(res_trilaterate),
							time: formattedTime
							

						},
						success: function(response) {
							alert('Данные успешно отправлены в базу данных!');
							resolve(response);
						}
						});
					});	
					
				}

				make_request()
				.then(function(result) {
					
				});

				
				

			});

			
			function calculateDistance(x1, y1, z1, x2, y2, z2) {
				var dx = x2 - x1;
				var dy = y2 - y1;
				var dz = z2 - z1;

				var distance = Math.sqrt(dx * dx + dy * dy + dz * dz);

				return distance;
			}


			function trilaterate(p1, p2, p3, return_middle) {
				
				function sqr(a)
				{
					return a * a;
				}
				
				function norm(a)
				{
					return Math.sqrt(sqr(a.x) + sqr(a.y) + sqr(a.z));
				}
				
				function dot(a, b)
				{
					return a.x * b.x + a.y * b.y + a.z * b.z;
				}
				
				function vector_subtract(a, b)
				{
					return {
						x: a.x - b.x,
						y: a.y - b.y,
						z: a.z - b.z
					};
				}
				
				function vector_add(a, b)
				{
					return {
						x: a.x + b.x,
						y: a.y + b.y,
						z: a.z + b.z
					};
				}
				
				function vector_divide(a, b)
				{
					return {
						x: a.x / b,
						y: a.y / b,
						z: a.z / b
					};
				}
				
				function vector_multiply(a, b)
				{
					return {
						x: a.x * b,
						y: a.y * b,
						z: a.z * b
					};
				}
				
				function vector_cross(a, b)
				{
					return {
						x: a.y * b.z - a.z * b.y,
						y: a.z * b.x - a.x * b.z,
						z: a.x * b.y - a.y * b.x
					};
				}
				
				var ex, ey, ez, i, j, d, a, x, y, z, b, p4a, p4b;
				
				ex = vector_divide(vector_subtract(p2, p1), norm(vector_subtract(p2, p1)));
				
				i = dot(ex, vector_subtract(p3, p1));
				a = vector_subtract(vector_subtract(p3, p1), vector_multiply(ex, i));
				ey = vector_divide(a, norm(a));
				ez =  vector_cross(ex, ey);
				d = norm(vector_subtract(p2, p1));
				j = dot(ey, vector_subtract(p3, p1));
				
				x = (sqr(p1.r) - sqr(p2.r) + sqr(d)) / (2 * d);
				y = (sqr(p1.r) - sqr(p3.r) + sqr(i) + sqr(j)) / (2 * j) - (i / j) * x;
				
				b = sqr(p1.r) - sqr(x) - sqr(y);
				
				
				if (Math.abs(b) < 0.0000000001)
				{
					b = 0;
				}
				
				z = Math.sqrt(b);
				
				
				if (isNaN(z))
				{
					return null;
				}
				
				a = vector_add(p1, vector_add(vector_multiply(ex, x), vector_multiply(ey, y)))
				p4a = vector_add(a, vector_multiply(ez, z));
				p4b = vector_subtract(a, vector_multiply(ez, z));
				
				if (z == 0 || return_middle)
				{
					return a;
				}
				else
				{
					return [ p4a, p4b ];
				}
			}
			
			

			function onPointerMove( event ) {

				if (checkbox_control.checked) {

					drag_controls.enabled = false;
					orbit_controls.enabled = false;

					pointer.set( ( event.clientX / window.innerWidth ) * 2 - 1, - ( event.clientY / window.innerHeight ) * 2 + 1 );
					raycaster.setFromCamera( pointer, camera );
					const intersects = raycaster.intersectObjects( objects, false );

					if ( intersects.length > 0 ) {

						const intersect = intersects[ 0 ];
						rollOverMesh.position.copy( intersect.point ).add( intersect.face.normal );

						if ( intersect.object == plane ) {
							
							rollOverMesh.position.round().add(new THREE.Vector3( 0.5, -0.25, 0.5 ));
						}

						else {
							rollOverMesh.position.round().add(new THREE.Vector3( 0.5, -0.4, 0.5 ));
						}
					

					}

				}

				else {
					
					drag_controls.enabled = true;
					
					pointer.set( ( event.clientX / window.innerWidth ) * 2 - 1, - ( event.clientY / window.innerHeight ) * 2 + 1 );
					raycaster.setFromCamera( pointer, camera );
					const intersects = raycaster.intersectObjects( cubes, false );

					if ( intersects.length > 0 ) {
						orbit_controls.enabled = false;
					}

					else {
						orbit_controls.enabled = true;
					}


				}

			}

			let count_name;
			
			
			
			function onPointerDown( event ) {
				const voxel = new THREE.Mesh( cubeGeo, cubeMaterial);
				let element;
				let label;
				let label2;
				

				label = new CSS2DObject(element);
				label2 = new CSS2DObject(element);
				
				

				if (checkbox_control.checked) {
					drag_controls.enabled =false;
					
					pointer.set( ( event.clientX / window.innerWidth ) * 2 - 1, - ( event.clientY / window.innerHeight ) * 2 + 1 );
					raycaster.setFromCamera( pointer, camera );
					let intersects = raycaster.intersectObjects( objects, false );

					if ( intersects.length > 0 ) {

						let intersect = intersects[ 0 ];

						if ( checkbox_add_or_remove.checked ) {

							if ( intersect.object != plane && intersect.object.name != 'horizontal') {

								container = document.getElementsByClassName('label '+ String(intersect.object.name));

								
								
								

								//delete from db

								console.log(intersect.object.name);

								function make_request() {
								return new Promise(function(resolve){
									$.ajax({
									url: './delete.php', 
									method: 'POST',
									data: {
										object_id: intersect.object.name
									},
									success: function(response) {
										
										//alert('Данные успешно отправлены в базу данных!');
										
										resolve(response);
									}
									});
								});	
								
							}

							make_request()
							.then(function(response) {

								if (container !== null) {
									let parent = container.item(0).parentNode;

									parent.removeChild(container.item(0));
									parent.removeChild(container.item(0));

									scene.remove( intersect.object );
								objects.splice( objects.indexOf( intersect.object ), 1 );

									
								}
								
								
								
							});

								
								
								

							}

							// create cube

						}

						

						else {
							let session = null;
							function get_session_value() {
								return new Promise(function(resolve){
									$.ajax({
									url: './get_session_value.php', 
									type: 'GET',
									success: function(response) {
										if (response.success) {
											
										}
										resolve(response);
									}
									});
								});
							}

							get_session_value()
							.then(function(response) {
								session = response.data;
								console.log(session);

								if (session != null) {

									label.element = document.createElement('div');
								
								
								
									voxel.position.copy( intersect.point ).add( intersect.face.normal );

									if ( intersect.object == plane ) {
										voxel.position.round().add(new THREE.Vector3( 0.5, -0.25, 0.5 ));
									}
									else {
										voxel.position.round().add(new THREE.Vector3( 0.5, -0.4, 0.5 ));
									}
											
									objects.push(voxel);
									cubes.push(voxel);

												
									label.element.innerHTML = 'x: ' + String(voxel.position.x.toFixed(3)) + '<br/>' + 'y: ' + String(voxel.position.y.toFixed(3)) + '<br/>' + 'z: ' + String(voxel.position.z.toFixed(3));
				
									label.position.z = label.position.z + 2;
									
									

									//let object_id = count_name;
									

									//trilaterate
									let res_trilaterate;
									let distance;
									let count = 0;
									let beacons_pos = [];
									let distances = [];
									for (let item of beacons) {
										distance = calculateDistance(item.position.x, item.position.y, item.position.z, voxel.position.x, voxel.position.y, voxel.position.z);
										if (distance < 15) {
											
											
											if (count == 3) break;
											
												beacons_pos.push(item.position);
												distances.push(distance);
											
											
											count++;
											
											
										};
										
										
										
									}
									for (let i = 0; i< beacons_pos.length; i++) {
										beacons_pos[i].r = distances[i];
										
									}
									//console.log(beacons_pos);
									//console.log(distances);
									
									
									res_trilaterate = trilaterate(beacons_pos[0], beacons_pos[1], beacons_pos[2], true);

									//console.log(res_trilaterate.x);
									
									label2.element = document.createElement('div');
									
									
									

									label2.element.innerHTML = 'x: ' + String(res_trilaterate.x.toFixed(3)) + '<br/>' + 'y: ' + String(res_trilaterate.y.toFixed(3)) + '<br/>' + 'z: ' + String(res_trilaterate.z.toFixed(3));
				
									label2.position.z = label2.position.z - 2;


									let now = new Date();

									
									let formattedTime = now.toLocaleString('ru-RU', { timeZone: 'Europe/Moscow' });
									
									// Выполняем AJAX-запрос
									// Отправляем данные через AJAX
									function make_request() {
										return new Promise(function(resolve){
											$.ajax({
											url: './insert.php', 
											method: 'POST',
											data: {
												real_current_location: JSON.stringify(voxel.position),
												current_location: JSON.stringify(res_trilaterate),
												time: formattedTime
												

											},
											success: function(response) {
												alert('Данные успешно отправлены в базу данных!');
												resolve(response);
											}
											});
										});	
										
									}

									make_request()
									.then(function(result) {
										
									});
									

									let result;

									function get_data() {
										return new Promise(function(resolve){
											$.ajax({
											url: './get_id.php', 
											type: 'GET',
											success: function(response) {
												if (response.success) {
													//count_name = response.data[1];
												}
												resolve(response);
											}
											});
										});
									}

									get_data()
									.then(function(response) {
										
										for (let item of response.data) {
											let pos = JSON.parse(item.real_current_location);
											

											if (pos.x == voxel.position.x && pos.y == voxel.position.y && pos.z == voxel.position.z) {
												voxel.name = item.object_id;
												label.element.classList.add('label', item.object_id);
												label2.element.classList.add('label', item.object_id);

												
												
												
												
											}
											
										}
										
									//console.log(voxel);
									//console.log(voxel.name);
									scene.add(voxel);
									voxel.add(label);
									voxel.add(label2);
									
									});
									
								}
								
								else {
									alert("signin first!");
								}
							});
						}	
							


							
							

							

							

															
						

						

						
					}
					
				}

				else {
					drag_controls.enabled =true;
				}

				

			}

			
			
					

			
			function onWindowResize() {
				camera.aspect = window.innerWidth / window.innerHeight;
				camera.updateProjectionMatrix();
				renderer.setSize( window.innerWidth, window.innerHeight );
				

			}

			function animate() {
				requestAnimationFrame( animate );
				orbit_controls.update(0.5); 
				
				
				
				render();

			}

			function render() {
				renderer.render( scene, camera );
				labelRenderer.render( scene, camera );
				
			}

			

		</script>
		
    </body>
</html>