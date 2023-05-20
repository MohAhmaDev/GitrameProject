import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom';
import axiosClient from '../axios-client';
import { useStateContext } from '../contexts/ContextProvider';
import { useForm } from 'react-hook-form';
import { useDisplayContext } from '../contexts/DisplayContext';
import { Button, CircularProgress } from '@mui/material';


export default function Users() {

  const {user, fetchUser, role, filiale, setNotification} = useStateContext()
  const {users, loading, getUsers} = useDisplayContext()


  const onDelete = (u) => {
    if (!window.confirm(`Are you sure you want to delete the user '${u.name}'`))
    {
      return;
    }
    axiosClient.delete(`/users/${u.id}`)
    .then(() => {
      setNotification('User deleted successfully');
      getUsers();
    })
  }

  useEffect(() => {
    getUsers()
    fetchUser()
  }, [])


  return (
    <div>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center'}}>
        <h1>Users</h1>
        {(role && role === "admin") && <Link to="/users/new" className='btn-add'> Add new </Link>}
      </div>
      <div className="card animated fadeInDown">
        <table>
          <thead>
          <tr>
            <th>Filiale</th>
            <th>Name</th>
            <th>role</th>
            <th>Create Date</th>
            <th> access </th>
            <th>Actions</th>
          </tr>
          </thead>
          {loading &&
            <tbody>
            <tr>
              <td colSpan="5" class="text-center">
                <CircularProgress disableShrink />
              </td>
            </tr>
            </tbody>
          }
          {!loading && 
            <tbody>
            {users.map(u => (
              <tr key={u.id}>
                <td>{!u.filiale?.id ? <Link to={`/users/filiale/${u.id}`}> ajouter </Link> 
                : u.filiale?.name}</td>
                <td>{u.name}</td>
                <td>{!u.role ? "no role assigned" : u.role}</td>
                <td>{u.created_at}</td>
                <td> TB-{u.admission?.table} </td>
                <td>
                  {(role && role === "admin") ? <Link className="btn-edit" to={'/users/' + u.id}>Edit</Link> : "no action"}
                  &nbsp;
                  {(role && role === "admin") && <button onClick={ev => onDelete(u)} className="btn-delete"> Delete </button>}
                </td>
              </tr>
            ))}
            </tbody>
          }
        </table>
      </div>
    </div>
  )
}
