import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom';
import axiosClient from '../axios-client';
import { useStateContext } from '../contexts/ContextProvider';
import { useForm } from 'react-hook-form';


export default function Users() {





  const [users, setUsers] = useState();
  const [loading, setLoading] = useState(true);
  const [role, setRole] = useState(null);
  const {user, setUser, setNotification} = useStateContext()

  useEffect(() => {
    getUsers()
  }, [])

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

  const getUsers = () => {
    setLoading(true)
    axiosClient.get('/users')
      .then(({ data }) => {
        setLoading(false)
        setUsers(data.data)
      })
      .catch(() => {
        setLoading(false)
      })
  }

  useEffect(() => {
    axiosClient.get('/user')
    .then(({data}) => {
        setUser(data.user)
        setRole(data.role);
    })
  }, [])

  // console.log("users : ", user)

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
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Create Date</th>
            <th>Actions</th>
          </tr>
          </thead>
          {loading &&
            <tbody>
            <tr>
              <td colSpan="5" class="text-center">
                Loading...
              </td>
            </tr>
            </tbody>
          }
          {!loading && 
            <tbody>
            {users.map(u => (
              <tr key={u.id}>
                <td>{u.id}</td>
                <td>{u.name}</td>
                <td>{!u.role ? "no role assigned" : u.role}</td>
                <td>{u.created_at}</td>
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
