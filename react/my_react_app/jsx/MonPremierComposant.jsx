import axios from "axios";
import React, { useEffect, useState } from "react";
import { Button, Carousel, CarouselItem, Spinner } from "react-bootstrap";


const MonPremierComposant = (props) => {
    //index du carrousel
    const [index,setIndex] = useState(0);
    //etat de l'animation du chargement des donnees
    const [loading,setLoading] = useState(true);
    const [users, setUsers] = useState([]);


    //set l'index du carrousel en cas de changement
    const handleSelect = (selectedIndex,e) => {
        setIndex(selectedIndex);
    };

    //get the 10 last articles from interim-info; déplacée ici le 13/06
    useEffect( () => {
        //call the 'liste_articles' route from AgenceCtrl
        axios.get("https://reqres.in/api/users?per_page=20").then((response)=>{
            setUsers(response.data.data);
            // console.log(response.data.data);
            setLoading(false);
        }).catch(thrown => {});

    }, []);


    return (
        <div className={"container"}>
            {
                users ? (

                ) : ()
            <div className="row">
                <div className={'col-12 mt-4'}>
                    <h1 className={'p-3 text-center'} style={{color: "#4169E1", fontSize: "2.8em"}}>Notre équipe</h1>
                    <hr />
                </div>
            </div>
                <Carousel activeIndex={index} onSelect={handleSelect} className={"mt-5"}>
                    {
                        users && users.map((item, index)=> (
                            <Carousel.Item className="w-100" key={index}>
                                <img
                                    className="carouselImage mr-2"
                                    src={item.avatar}
                                    alt={"photo-user"}
                                    style={{height: "240px" }}
                                />
                                <Carousel.Caption>
                                <span className="text-white rounded">
                                    <h2 style={{color: "#4169E1", fontSize: "2.5em", textShadow: "2px 2px 4px #4169E1"}}>{item.first_name} {item.last_name}</h2>
                                    <p style={{color: "#4169E1", fontSize: "2em"}}>{item.email}</p>
                                </span>
                                </Carousel.Caption>
                            </Carousel.Item>)
                        )
                    }

                </Carousel>
        </div>
    );

}

export default MonPremierComposant