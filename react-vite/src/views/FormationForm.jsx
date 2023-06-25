import React, { useEffect, useState } from "react";
import { useForm, Controller, useFormContext } from "react-hook-form";

import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs';
import { DesktopDatePicker, DatePicker, LocalizationProvider } from "@mui/x-date-pickers";
import { 
  FormControl,
  InputLabel,
  Select,
  MenuItem,
  Box,
  useMediaQuery,
  Button,
  TextField,
  RadioGroup,
  Radio,
  FormControlLabel,
  FormLabel,
  CircularProgress
} from "@mui/material";

import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from "yup";
import axiosClient from "../axios-client";
import { useNavigate, useParams } from "react-router-dom";
import dayjs from "dayjs";
import { useStateContext } from "../contexts/ContextProvider";
import { useDisplayContext } from "../contexts/DisplayContext";
import { useFormsContext } from "../contexts/FormsContext";
const FormationForm = () => {

    const {id} = useParams()
    const [formation, setFormation] = useState({
      domaine_formation: "",
      diplomes_obtenues: "",
      intitule_formation: "",
      duree_formation: null,
      montant: null,
      lieu_formation: "",
      employe_id: null,
    })
  
    const {setNotification, filiale, role, user, fetchUser} = useStateContext()
    const { employes, getEmployes, getFormations } = useFormsContext()
  
    const useScheme = yup.object({
      domaine_formation: yup.string().required('ce champ est obligatoire'),
      diplomes_obtenues: yup.string().required("ce champ est obligatoire"),
      intitule_formation: yup.string().required('ce champ est obligatoire'),
      lieu_formation: yup.string().required('ce champ est obligatoire'),
      duree_formation: yup.number().required('veillez ajouter une valeur a ce champs'),
      montant: yup.number().required('ce champ est obligatoire'),
      employe_id: yup.number('number only').required()
    })
    const { handleSubmit, control, setError, reset, getValues, setValue, register, formState, formState: { errors } } = useForm({
      mode: "onChange",
      resolver: yupResolver(useScheme)
    });
  
    const {isSubmitting, isValid, isSubmitted, isSubmitSuccessful} = formState                   
    const isNonMobile = useMediaQuery("(min-width:600px)");
    const navigate = useNavigate();
    const [loading, setLoading] = useState(false);
    
  
    const onSubmit = (data) => {
      if (formation.id) 
      {
        console.log("data : ", data)
        const updateFormation = {
          employe_id: data.employe_id,
          domaine_formation: data.domaine_formation,
          diplomes_obtenues: data.diplomes_obtenues,
          intitule_formation: data.intitule_formation,
          lieu_formation: data.lieu_formation,
          duree_formation: data.duree_formation,
          montant: data.montant,
        }
        axiosClient.put(`/formations/${formation.id}`, updateFormation)
        .then(() => {
          //TODO show notification
          setNotification("formation was successfuly Update")
          navigate('/formations')
        })
        .catch(err => {
          const response = err.response;
          if (response && response.status === 422) {
            setError('server', {
              message: response?.data.errors
            })        
          }
          console.log(err)
        })
      } 
      else 
      {
        const Add_formation = {
          employe_id: data.employe_id,
          domaine_formation: data.domaine_formation,
          diplomes_obtenues: data.diplomes_obtenues,
          intitule_formation: data.intitule_formation,
          lieu_formation: data.lieu_formation,
          duree_formation: data.duree_formation,
          montant: data.montant,
        }
        console.log(Add_formation)
        axiosClient.post('/formations', Add_formation)
        .then(() => {
          setNotification("le formation à bien été saiséer")
          navigate('/formations')
        })
        .catch((err) => {
          console.log(err)
          const response = err.response;
          if (response && response.status === 422) {
            setError('server', {
              message: response.response.data.errors
            })       
          }
        })
      }  
    }
  
  
    const getFormation = () => {
      setLoading(true)
      axiosClient.get(`formations/${id}`)
      .then(({data}) => {
        setFormation({
          id: id,
          employe_id: data.data.employe_id,
          domaine_formation: data.data.domaine_formation,
          diplomes_obtenues: data.data.diplomes_obtenues,
          intitule_formation: data.data.intitule_formation,
          lieu_formation: data.data.lieu_formation,
          duree_formation: data.data.duree_formation,
          montant: data.data.montant,
        })
        reset({...data.data})
        setLoading(false)
      })
      .catch((err) => {
        console.log(err)
      })
    }
  
  
    if (id) {
      useEffect(() => {
        getFormation()
        console.log("id: ", id)
      }, [])
    }
  
    useEffect(() => {
      getEmployes()
      fetchUser()
    }, [])
  
    return (
      <>
        {!loading && <div className="card animated fadeInDown">
          <Box mt="20px">
            <form onSubmit={handleSubmit(onSubmit)}>
              <Box
                display="grid" 
                gap="30px" 
                gridTemplateColumns="repeat(6, minmax(0, 1fr))"
              >

                <TextField 
                  label="domaine de formation"
                  {...register("domaine_formation")}
                  onChange={ev => setFormation({...formation, domaine_formation: ev.target.value})}
                  value={formation.domaine_formation}
                  variant="outlined"
                  sx={{ gridColumn: "span 2" }}
                  error={errors.domaine_formation ? true : false}
                  helperText={errors.domaine_formation && errors.domaine_formation?.message}
                />
                <TextField 
                  label="diplomes obtenues"
                  {...register("diplomes_obtenues")}
                  onChange={ev => setFormation({...formation, diplomes_obtenues: ev.target.value})}
                  value={formation.diplomes_obtenues}
                  variant="outlined"
                  sx={{ gridColumn: "span 2" }}
                  error={errors.diplomes_obtenues ? true : false}
                  helperText={errors.diplomes_obtenues && errors.diplomes_obtenues?.message}
                />
  
                <TextField
                  label="intitule de formation"
                  {...register("intitule_formation")}
                  onChange={ev => setFormation({...formation, intitule_formation: ev.target.value})}
                  value={formation.intitule_formation}
                  variant="outlined"
                  sx={{ gridColumn: "span 2" }}
                  error={errors.intitule_formation ? true : false}
                  helperText={errors.intitule_formation && errors.intitule_formation?.message}
                />
                <TextField 
                  label="lieu de formation"
                  {...register("lieu_formation")}
                  onChange={ev => setFormation({...formation, lieu_formation: ev.target.value})}
                  value={formation.lieu_formation}
                  variant="outlined"
                  sx={{ gridColumn: "span 2" }}
                  error={errors.lieu_formation ? true : false}
                  helperText={errors.lieu_formation && errors.lieu_formation?.message}
                />
                <TextField
                  type="number"               
                  label="duree de formation"
                  {...register("duree_formation")}
                  onChange={ev => setFormation({...formation, duree_formation: ev.target.value})}
                  value={formation.duree_formation}
                  variant="outlined"
                  sx={{ gridColumn: "span 2" }}
                  error={errors.duree_formation ? true : false}
                  helperText={errors.duree_formation && errors.duree_formation?.message}
                />
                <TextField 
                  type="number"
                  label="montant"
                  {...register("montant")}
                  onChange={ev => setFormation({...formation, montant: ev.target.value})}
                  value={formation.montant}
                  variant="outlined"
                  sx={{ gridColumn: "span 2" }}
                  error={errors.montant ? true : false}
                  helperText={errors.montant && errors.montant?.message}
                />
                <FormControl variant="outlined" sx={{ width: "300px" }}>
                  <InputLabel id="demo-simple-select-label"> employe  </InputLabel>
                  <Select
                    value={formation.employe_id}
                    {...register("employe_id")}
                    sx={{ gridColumn: "span 2" }}
                    label="employe"
                    onChange={ev => setFormation({...formation, employe_id: ev.target?.value})}
                    error={errors.employe_id ? true : false}

                  >
                    {((!!employes) && (Object.keys(employes).length !== 0)) && employes?.map(employe => (
                      <MenuItem value={employe.id} key={employe.id}> {employe.nom} </MenuItem>
                    ))}
                  </Select>
                  {errors.employe_id && <span style={{
                    color: "#d32f2f",
                    fontSize: "0.75em",
                    textAlign: "left",
                    fontWeight: "400"
                  }}> {errors.employe_id?.message} </span>}
                </FormControl>
  
              </Box>
              <Box display="flex" justifyContent="end" mt="20px">
                <Button  type="submit" color="success" variant="contained">
                    {id ? "Update" : "Create New" }  formation
                </Button>
              </Box>
            </form>
          </Box>
          {errors?.server?.message &&
            <div className="alert">
              {Object.keys(errors?.server?.message).map(key => (
                <p>{errors?.server?.message[key][0]}</p>
              ))}
            </div>}
        </div>}
        {loading && <CircularProgress disableShrink /> }
      </>
    )
  
};

export default FormationForm;